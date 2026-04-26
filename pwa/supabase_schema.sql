-- KEYSTONE POSSIBILITIES - PWA SUPABASE SCHEMA
-- Execute this directly in the Supabase SQL Editor

-- ==========================================
-- 1. ENUMS (Custom Data Types)
-- ==========================================
CREATE TYPE user_role AS ENUM ('owner', 'pm', 'trade', 'architect', 'city_inspector');
CREATE TYPE doc_type AS ENUM ('wcb', 'liability_insurance', 'subcontractor_agreement', 'pm_contract', 'permit', 'invoice', 'site_photo', 'inspection_report', 'change_order');
CREATE TYPE doc_status AS ENUM ('missing', 'action_needed', 'pending_approval', 'active', 'rejected');
CREATE TYPE financial_status AS ENUM ('pending', 'approved', 'paid', 'denied');
CREATE TYPE project_status AS ENUM ('planning', 'permitting', 'active_construction', 'deficiency', 'completed');

-- ==========================================
-- 2. TABLES
-- ==========================================

-- USERS TABLE (Extends Supabase auth.users)
CREATE TABLE public.users (
  id UUID REFERENCES auth.users(id) ON DELETE CASCADE PRIMARY KEY,
  email TEXT UNIQUE NOT NULL,
  full_name TEXT NOT NULL,
  company_name TEXT,
  role user_role NOT NULL DEFAULT 'trade',
  phone TEXT,
  is_approved BOOLEAN NOT NULL DEFAULT false,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- PROJECTS TABLE
CREATE TABLE public.projects (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  owner_id UUID REFERENCES public.users(id),
  pm_id UUID REFERENCES public.users(id),
  title TEXT NOT NULL,
  address TEXT,
  status project_status NOT NULL DEFAULT 'planning',
  total_budget NUMERIC(12,2),
  pm_fee_percentage NUMERIC(5,2) DEFAULT 12.00,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- TRADES / WORKSPACES TABLE (Maps a Trade User to a Project)
CREATE TABLE public.project_trades (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  project_id UUID REFERENCES public.projects(id) ON DELETE CASCADE,
  user_id UUID REFERENCES public.users(id) ON DELETE CASCADE,
  trade_category TEXT NOT NULL, -- e.g., "Demolition & Excavation"
  locked_bid NUMERIC(12,2),
  status TEXT DEFAULT 'pending', -- pending, active, completed
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  UNIQUE(project_id, user_id, trade_category)
);

-- DOCUMENTS & COMPLIANCE VAULT
CREATE TABLE public.documents (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  project_id UUID REFERENCES public.projects(id) ON DELETE CASCADE,
  trade_id UUID REFERENCES public.project_trades(id) ON DELETE CASCADE, -- Null if it's a general project doc
  uploaded_by UUID REFERENCES public.users(id),
  document_type doc_type NOT NULL,
  title TEXT NOT NULL,
  file_url TEXT NOT NULL,
  status doc_status NOT NULL DEFAULT 'pending_approval',
  expires_at TIMESTAMPTZ, -- Important for WCB and Liability
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- FINANCIAL LEDGER (Invoices, Payments, Change Orders)
CREATE TABLE public.financial_ledger (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  project_id UUID REFERENCES public.projects(id) ON DELETE CASCADE,
  trade_id UUID REFERENCES public.project_trades(id),
  requested_by UUID REFERENCES public.users(id),
  type TEXT NOT NULL, -- 'invoice', 'change_order', 'payment'
  amount NUMERIC(12,2) NOT NULL,
  description TEXT,
  status financial_status NOT NULL DEFAULT 'pending',
  approved_by UUID REFERENCES public.users(id),
  approved_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- IMMUTABLE COMMUNICATION LOG
CREATE TABLE public.communications (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  project_id UUID REFERENCES public.projects(id) ON DELETE CASCADE,
  trade_id UUID REFERENCES public.project_trades(id), -- To isolate chat to a specific trade workspace
  sender_id UUID REFERENCES public.users(id),
  message TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
  -- No UPDATE or DELETE allowed on this table by design
);

-- MASTER CALENDAR MILESTONES
CREATE TABLE public.milestones (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  project_id UUID REFERENCES public.projects(id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  description TEXT,
  start_date DATE NOT NULL,
  end_date DATE,
  duration_days INTEGER NOT NULL DEFAULT 1,
  sequence_order INTEGER NOT NULL DEFAULT 0,
  status TEXT DEFAULT 'pending',
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- DATE PROPOSALS (Trades propose alternate dates for milestones)
CREATE TABLE public.date_proposals (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  milestone_id UUID REFERENCES public.milestones(id) ON DELETE CASCADE,
  trade_id UUID REFERENCES public.users(id),
  proposed_start_date DATE NOT NULL,
  status TEXT DEFAULT 'pending', -- pending, approved, denied
  pm_notes TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ==========================================
-- 3. ROW LEVEL SECURITY (RLS)
-- ==========================================
ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.projects ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.project_trades ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.documents ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.financial_ledger ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.communications ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.milestones ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.date_proposals ENABLE ROW LEVEL SECURITY;

-- Basic Security Policies:
-- 1. PMs and Owners can view everything in their projects.
-- 2. Trades can ONLY view data where their user_id matches (Strict Isolation).

CREATE POLICY "Users can view their own profile" 
ON public.users FOR SELECT USING (auth.uid() = id);

-- Helper functions to prevent infinite recursion in RLS
CREATE OR REPLACE FUNCTION public.is_project_owner_or_pm(proj_id UUID)
RETURNS BOOLEAN
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1 FROM public.projects
    WHERE id = proj_id AND (owner_id = auth.uid() OR pm_id = auth.uid())
  );
$$;

CREATE OR REPLACE FUNCTION public.is_assigned_to_project(proj_id UUID)
RETURNS BOOLEAN
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1 FROM public.project_trades
    WHERE project_id = proj_id AND user_id = auth.uid()
  );
$$;

CREATE POLICY "Trades can view their assigned projects"
ON public.projects FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    owner_id = auth.uid() 
    OR pm_id = auth.uid()
    OR public.is_assigned_to_project(id)
  )
);

CREATE POLICY "Owners and PMs can create projects"
ON public.projects FOR INSERT WITH CHECK (
  (SELECT role FROM public.users WHERE id = auth.uid()) IN ('owner', 'pm')
);

CREATE POLICY "Owners and PMs can update projects"
ON public.projects FOR UPDATE USING (
  (SELECT role FROM public.users WHERE id = auth.uid()) IN ('owner', 'pm')
);

CREATE POLICY "Trades can only view their own workspace data"
ON public.project_trades FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    user_id = auth.uid() OR 
    public.is_project_owner_or_pm(project_id)
  )
);

CREATE POLICY "Financial ledger is only visible to owner and PM"
ON public.financial_ledger FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    public.is_project_owner_or_pm(project_id)
  )
);

CREATE POLICY "Documents visible to assigned trades, owner, and PM"
ON public.documents FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    public.is_project_owner_or_pm(project_id) OR
    trade_id IN (SELECT id FROM public.project_trades WHERE user_id = auth.uid()) OR
    (trade_id IS NULL AND public.is_assigned_to_project(project_id))
  )
);

CREATE POLICY "Communications visible to assigned trades, owner, and PM"
ON public.communications FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    public.is_project_owner_or_pm(project_id) OR
    trade_id IN (SELECT id FROM public.project_trades WHERE user_id = auth.uid())
  )
);

CREATE POLICY "Milestones visible to assigned trades, owner, and PM"
ON public.milestones FOR SELECT USING (
  (SELECT is_approved FROM public.users WHERE id = auth.uid()) = true AND (
    public.is_project_owner_or_pm(project_id) OR
    public.is_assigned_to_project(project_id)
  )
);

CREATE POLICY "Owners and PMs can manage milestones"
ON public.milestones FOR ALL USING (
  (SELECT role FROM public.users WHERE id = auth.uid()) IN ('owner', 'pm')
);

-- ==========================================
-- 4. TRIGGERS (Auto-updates)
-- ==========================================
-- Automatically update updated_at timestamp
CREATE OR REPLACE FUNCTION update_modified_column() 
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW; 
END;
$$ language 'plpgsql';

CREATE TRIGGER update_user_modtime 
BEFORE UPDATE ON public.users 
FOR EACH ROW EXECUTE PROCEDURE update_modified_column();

-- Automatically create public.users row on auth.users insert
CREATE OR REPLACE FUNCTION public.handle_new_user() 
RETURNS TRIGGER AS $$
DECLARE
  -- Add your admin emails here (comma-separated). These accounts are always auto-approved as PM.
  admin_emails TEXT[] := ARRAY['curtis4vancouver@gmail.com'];
  user_role_val user_role;
  user_approved BOOLEAN;
BEGIN
  -- If the email matches an admin, auto-approve as PM
  IF NEW.email = ANY(admin_emails) THEN
    user_role_val := 'pm';
    user_approved := true;
  ELSE
    user_role_val := 'trade';
    user_approved := false;
  END IF;

  INSERT INTO public.users (id, email, full_name, role, is_approved)
  VALUES (
    NEW.id,
    NEW.email,
    COALESCE(NEW.raw_user_meta_data->>'full_name', 'New User'),
    user_role_val,
    user_approved
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();

-- ==========================================
-- 5. ADMIN UTILITIES
-- ==========================================
-- Security definer function to allow the first user (or anyone with the key) to become an admin.
CREATE OR REPLACE FUNCTION public.claim_admin_role(secret_key TEXT)
RETURNS BOOLEAN AS $$
DECLARE
  -- Hardcoded master key for the owner to elevate their account
  expected_key TEXT := 'keystone_master_2026'; 
  user_email TEXT;
  user_name TEXT;
BEGIN
  IF secret_key = expected_key THEN
    -- Get email and name from auth.users
    SELECT email, COALESCE(raw_user_meta_data->>'full_name', 'System Admin') 
    INTO user_email, user_name 
    FROM auth.users WHERE id = auth.uid();

    -- Upsert the user to ensure they exist in public.users
    INSERT INTO public.users (id, email, full_name, role, is_approved)
    VALUES (auth.uid(), user_email, user_name, 'pm', true)
    ON CONFLICT (id) DO UPDATE 
    SET role = 'pm', is_approved = true;

    RETURN true;
  END IF;
  RETURN false;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
