import { createClient } from '@supabase/supabase-js';

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

const supabase = createClient(supabaseUrl, supabaseKey);

async function main() {
  const { data: authData, error: authError } = await supabase.auth.signInWithPassword({
    email: 'testuser@example.com',
    password: 'Password123!',
  });
  
  if (authError) {
    console.error('Auth Error:', authError.message);
    return;
  }
  console.log('Logged in as:', authData.user.id);
  
  const { data, error } = await supabase.from('users').select('*');
  console.log('Users (as authenticated user):', data);
  console.log('Error:', error);
}

main();
