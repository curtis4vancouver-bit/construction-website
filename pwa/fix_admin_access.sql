-- RUN THIS IN SUPABASE SQL EDITOR (one-time fix)
-- This updates your existing account AND the trigger for future signups.

-- Step 1: Fix your existing account to PM + approved
UPDATE public.users 
SET role = 'pm', is_approved = true 
WHERE email = 'curtis4vancouver@gmail.com';

-- Step 2: Update the trigger so future signups auto-detect admin emails
CREATE OR REPLACE FUNCTION public.handle_new_user() 
RETURNS TRIGGER AS $$
DECLARE
  admin_emails TEXT[] := ARRAY['curtis4vancouver@gmail.com'];
  user_role_val user_role;
  user_approved BOOLEAN;
BEGIN
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

-- Step 3: Update claim_admin_role to give PM instead of owner
CREATE OR REPLACE FUNCTION public.claim_admin_role(secret_key TEXT)
RETURNS BOOLEAN AS $$
DECLARE
  expected_key TEXT := 'keystone_master_2026'; 
  user_email TEXT;
  user_name TEXT;
BEGIN
  IF secret_key = expected_key THEN
    SELECT email, COALESCE(raw_user_meta_data->>'full_name', 'System Admin') 
    INTO user_email, user_name 
    FROM auth.users WHERE id = auth.uid();

    INSERT INTO public.users (id, email, full_name, role, is_approved)
    VALUES (auth.uid(), user_email, user_name, 'pm', true)
    ON CONFLICT (id) DO UPDATE 
    SET role = 'pm', is_approved = true;

    RETURN true;
  END IF;
  RETURN false;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
