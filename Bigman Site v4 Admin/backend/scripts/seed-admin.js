'use strict';

// ═══════════════════════════════════════════════════════════════
// SEED SCRIPT — Creates the first admin account
// Run ONCE with: node scripts/seed-admin.js
// ═══════════════════════════════════════════════════════════════

require('dotenv').config({ path: require('path').join(__dirname, '../.env') });
const mongoose = require('mongoose');
const User     = require('../models/User');

async function seed() {
  try {
    console.log('Connecting to MongoDB...');
    await mongoose.connect(process.env.MONGO_URI);
    console.log('Connected.\n');

    const email    = process.env.ADMIN_EMAIL    || 'admin@bigmanacademicservices.com';
    const password = process.env.ADMIN_PASSWORD || 'ChangeThisPassword123!';
    const name     = process.env.ADMIN_NAME     || 'Bigman Admin';
    const [firstName, ...rest] = name.split(' ');
    const lastName = rest.join(' ') || 'Admin';

    const existing = await User.findOne({ email });
    if (existing) {
      if (existing.role === 'admin') {
        console.log(`✅  Admin already exists: ${email}`);
      } else {
        existing.role = 'admin';
        await existing.save();
        console.log(`✅  Upgraded existing user to admin: ${email}`);
      }
    } else {
      await User.create({ firstName, lastName, email, password, role: 'admin', isEmailVerified: true });
      console.log(`✅  Admin account created successfully!`);
      console.log(`    Email:    ${email}`);
      console.log(`    Password: (as set in .env ADMIN_PASSWORD)`);
    }

    console.log('\n⚠️  IMPORTANT: Change the admin password immediately after first login!');
    process.exit(0);
  } catch (err) {
    console.error('❌  Seed failed:', err.message);
    process.exit(1);
  }
}

seed();
