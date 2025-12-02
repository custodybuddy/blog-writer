Phase 1: Strategic Foundation (Unchanged)
Your 10 pre-researched topics are perfect. Skip to Phase 2.

Phase 2: Technical Setup - Hostinger Specific
Step 1: Gather Required Accounts
You need:

✅ Hostinger account (you have this)
✅ Domain configured (custodybuddy.com - you have this)
✅ Claude Console account (console.anthropic.com)
✅ Amazon Associates account
⚠️ GitHub (optional but highly recommended for backups)

Step 2: Configure Cursor

Download and install Cursor
Create empty folder: custody-buddy-blog
Open folder in Cursor
Connect to Claude Sonnet 4.5 in settings
Add your Claude API key


Phase 3: Build the Blog System - Hostinger Version
Step 3: Generate Initial Codebase (Modified Prompt)
Switch Cursor to Planning Mode, then use this adapted prompt:
I'd like to create a blog that automatically updates. I'd like to use PHP, 
MySQL, and Claude to do the writing.

IMPORTANT HOSTING DETAILS:
- Hosting: Hostinger hPanel (shared hosting)
- Domain path: https://custodybuddy.com/family-law-blog/
- Server path: /public_html/family-law-blog/
- Database: MySQL (not SQLite)
- PHP version: 8.1 or 8.2 (check in Hostinger)
- Files will be uploaded via Hostinger File Manager
- Cron jobs configured via hPanel (not external service)

Blog Details:
- Topic: High-conflict co-parenting, parallel parenting, and family law navigation
- Audience: Parents navigating contentious custody situations, often dealing with 
  narcissistic or high-conflict ex-partners
- Tone: Empathetic but practical, validating without victimhood, solution-focused 
  with realistic expectations

Requirements:
- Minimal, mobile-first design
- Match the look and feel of: https://www.highconflictinstitute.com/blog
- Blog explores topics like: grey rock communication, parallel parenting strategies, 
  court documentation, protecting children, setting boundaries, emotional regulation
- Create one blog post per day (one every 5 minutes for testing)
- Include section at bottom with Amazon affiliate links to 3 relevant books
- Use PHP + MySQL (shared hosting compatible)
- AI will write posts AND select relevant books
- Must work in subdirectory structure (/family-law-blog/)
- Include database setup SQL file
- Include config.php for easy environment setup

Structure Requirements:
- index.php (blog homepage)
- post.php (individual post view)
- cron.php (for automated posting)
- admin.php (simple admin panel - optional but recommended)
- config.sample.php (template for configuration)
- install.sql (database setup script)
- .htaccess (for clean URLs)
- /assets/ folder (CSS, JS)

Database Requirements:
- Connection should use mysqli or PDO
- Include proper error handling
- Support shared hosting environment
- Auto-create tables if they don't exist

Additional Notes:
- Posts should be 800-1200 words
- Include actionable steps and specific scripts/templates
- Validate reader's experience before offering solutions
- Include disclaimers (not legal advice, not therapy)
- All file paths must be relative to /family-law-blog/ subdirectory

What questions do you have?
Answer AI's Questions:

MySQL Connection: "I'll provide database credentials after you generate the code. Use config.php for credentials."
Subdirectory Handling: "Yes, all URLs must work under /family-law-blog/ path. Use relative paths or BASE_URL constant."
File Upload Method: "Files will be uploaded manually via Hostinger File Manager. Keep structure simple."
Cron Job: "Hostinger hPanel has built-in cron job manager. Provide instructions for that."
PHP Version: "Assume PHP 8.1. Use modern PHP but avoid features that won't work on shared hosting."

Review the Plan → Click "Build"

Phase 4: Configure Hostinger Environment
Step 4: Set Up MySQL Database in hPanel
In Hostinger hPanel:

Navigate to Databases → MySQL Databases
Click Create New Database
Fill in:

   Database Name: u123456789_custody_blog
   (Hostinger auto-prefixes with your user ID)

Click Create
Create Database User:

   Username: u123456789_custody_user
   Password: [Generate strong password - save this!]

Click Create User
Assign User to Database:

Select database: u123456789_custody_blog
Select user: u123456789_custody_user
Grant All Privileges
Click Add


Save These Credentials:

   Database Host: localhost (on Hostinger)
   Database Name: u123456789_custody_blog
   Database User: u123456789_custody_user
   Database Password: [your generated password]
Step 5: Configure API Keys Locally
Create config.php in your local Cursor folder:
Ask Cursor:
Please create a config.php file with all necessary configuration. 
Include placeholders for:

- MySQL database credentials
- Claude API key
- Amazon Affiliate ID
- Cron secret token (32 hex characters)
- Base URL (https://custodybuddy.com/family-law-blog/)
- Subdirectory path (/family-law-blog/)
- Timezone settings

Also create config.sample.php (same file but with placeholder values) 
that I can commit to GitHub without exposing credentials.
Fill in your actual config.php:
php<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_custody_blog');
define('DB_USER', 'u123456789_custody_user');
define('DB_PASS', 'your_strong_password_here');

// API Keys
define('CLAUDE_API_KEY', 'sk-ant-your-key-here');
define('AMAZON_AFFILIATE_ID', 'custodybuddy-20');

// Security
define('CRON_SECRET_TOKEN', 'your_32_hex_character_token');

// Site Configuration
define('BASE_URL', 'https://custodybuddy.com/family-law-blog/');
define('SITE_PATH', '/family-law-blog/');
define('TIMEZONE', 'America/New_York'); // Adjust to your timezone

// Error Reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
```

**Get your API keys:**

**Claude API:**
- Go to console.anthropic.com
- Create new API key
- Paste into config.php

**Amazon Affiliate ID:**
- Sign up at Amazon Associates
- Your tag: `custodybuddy-20` (or your assigned tag)

**Cron Token:**
Ask Cursor: `Generate a secure 32 hex character token for CRON_SECRET_TOKEN`

---

## Phase 5: Local Testing

### Step 6: Test Locally with XAMPP/MAMP (Optional but Recommended)

**Option A: Test Locally First**

1. Install **XAMPP** (Windows) or **MAMP** (Mac)
2. Start Apache and MySQL
3. Import `install.sql` to local MySQL
4. Configure local `config.php` with local DB credentials
5. Access: `http://localhost/family-law-blog/`

**Option B: Skip to Live Testing**

If you're comfortable, skip local testing and go straight to Hostinger upload.

### Step 7: Initial File Organization Check

**Ask Cursor to verify structure:**
```
Please verify that all file paths are correct for subdirectory deployment:

1. All CSS/JS links should be relative or use BASE_URL
2. All internal links use BASE_URL . 'page.php'
3. .htaccess handles subdirectory routing correctly
4. Database connection includes proper error handling
5. No hardcoded paths like /var/www/html/

Show me the directory structure and confirm it's ready for upload.
```

**Expected structure:**
```
custody-buddy-blog/
├── index.php
├── post.php
├── cron.php
├── admin.php (optional)
├── config.php
├── config.sample.php
├── install.sql
├── .htaccess
├── README.md
├── /assets/
│   ├── /css/
│   │   └── style.css
│   └── /js/
│       └── main.js
└── /includes/
    ├── db.php
    ├── functions.php
    └── header.php
    └── footer.php

Phase 6: Upload to Hostinger
Step 8: Upload Files via File Manager
In Hostinger hPanel:

Navigate to Files → File Manager
Navigate to /public_html/family-law-blog/

If folder doesn't exist, create it:

Click + New Folder
Name: family-law-blog
Click Create




Upload files:
Option A: Upload as ZIP (Recommended)

In your local folder, select all files
Create ZIP: blog-files.zip
In File Manager, click Upload
Upload blog-files.zip
Right-click ZIP → Extract
Delete ZIP file after extraction

Option B: Upload Individual Files

Click Upload
Select all files from your local folder
Wait for upload to complete
Verify all files present


Set Permissions:

Select config.php → Right-click → Permissions
Set to 644 (read-only for security)
For uploads folder (if exists): 755



Step 9: Set Up Database
In Hostinger File Manager:

Locate install.sql in /public_html/family-law-blog/
Copy its contents

In Hostinger hPanel:

Navigate to Databases → phpMyAdmin
Select your database: u123456789_custody_blog
Click SQL tab
Paste contents of install.sql
Click Go
Verify tables created (check left sidebar)

Expected tables:

posts
topics
books
post_topics (if normalized)
settings (optional)

Step 10: Initial Configuration Check
Test database connection:

Navigate to: https://custodybuddy.com/family-law-blog/

Expected outcomes:
✅ Success: Blog homepage loads, shows empty state or "No posts yet"
❌ Error: Database connection failed

Fix: Check config.php credentials match hPanel MySQL settings
Check: DB_HOST is 'localhost' (correct for Hostinger)

❌ Error: 404 Not Found

Fix: Check .htaccess file uploaded correctly
Fix: Verify folder name is exactly family-law-blog

❌ Error: 500 Internal Server Error

Fix: Check PHP error log in hPanel → Logs
Common cause: Syntax error or wrong PHP version
Check: Hostinger PHP version (should be 8.1+)

Step 11: Load Your 10 Topics into Database
Option A: Via Admin Panel (if built)

Navigate to https://custodybuddy.com/family-law-blog/admin.php
Log in (or create admin access)
Go to "Topics" or "Content Management"
Paste your JSON topics:

json[
  {
    "title": "When Your Co-Parent Keeps Breaking the Parenting Plan",
    "description": "How to respond calmly, document properly, and protect your kids when the other parent constantly ignores the schedule or orders.",
    "category": "documentation",
    "used": false
  },
  {
    "title": "Grey Rocking Without Going Numb: Communication in High-Conflict Co-Parenting",
    "description": "Using neutral, low-drama responses to reduce conflict without abandoning your own emotions or needs.",
    "category": "communication",
    "used": false
  },
  {
    "title": "Parallel Parenting vs. Co-Parenting: What Works When Your Ex Won't Cooperate",
    "description": "Understanding the difference between co-parenting and parallel parenting, and when to switch strategies in a high-conflict case.",
    "category": "strategy",
    "used": false
  },
  {
    "title": "How to Document Emotional Abuse Without Looking Petty in Court",
    "description": "Practical ways to capture patterns of emotional harm, including text messages, school issues, and your child's statements, in a court-friendly way.",
    "category": "documentation",
    "used": false
  },
  {
    "title": "Supporting Your Child When They're Stuck in the Middle",
    "description": "Helping kids who feel torn, pressured to choose sides, or used as messengers between parents.",
    "category": "child-support",
    "used": false
  },
  {
    "title": "The Justice Gap: Why Court Doesn't Punish Every Lie and What to Do Instead",
    "description": "Accepting that family court is about best interests, not moral justice, and shifting your strategy accordingly.",
    "category": "legal-reality",
    "used": false
  },
  {
    "title": "Creating a FU Binder (Litigation Log) That Actually Works in Court",
    "description": "Turning chaos into clarity with evidence logs, timelines, and neutral language that judges can quickly understand.",
    "category": "documentation",
    "used": false
  },
  {
    "title": "Talking to Your Lawyer So They Actually Understand the Pattern of Abuse",
    "description": "How to summarize a long, messy history into clear themes, incidents, and evidence your lawyer can use.",
    "category": "legal-strategy",
    "used": false
  },
  {
    "title": "Protecting Your Nervous System in a Never-Ending Custody Battle",
    "description": "Simple, realistic self-regulation tools for parents who feel constantly flooded, hypervigilant, or exhausted.",
    "category": "self-care",
    "used": false
  },
  {
    "title": "Setting Boundaries Around Last-Minute Change Requests",
    "description": "Scripts and strategies for responding when the other parent constantly asks to swap days or change pickups at the last second.",
    "category": "boundaries",
    "used": false
  }
]
Option B: Direct Database Insert via phpMyAdmin

Hostinger hPanel → Databases → phpMyAdmin
Select u123456789_custody_blog
Click topics table
Click Insert tab
Manually add each topic, or:

Better: Use SQL Insert:
Click SQL tab, paste:
sqlINSERT INTO topics (title, description, category, used, times_used) VALUES
('When Your Co-Parent Keeps Breaking the Parenting Plan', 'How to respond calmly, document properly, and protect your kids when the other parent constantly ignores the schedule or orders.', 'documentation', 0, 0),
('Grey Rocking Without Going Numb: Communication in High-Conflict Co-Parenting', 'Using neutral, low-drama responses to reduce conflict without abandoning your own emotions or needs.', 'communication', 0, 0),
('Parallel Parenting vs. Co-Parenting: What Works When Your Ex Won''t Cooperate', 'Understanding the difference between co-parenting and parallel parenting, and when to switch strategies in a high-conflict case.', 'strategy', 0, 0),
('How to Document Emotional Abuse Without Looking Petty in Court', 'Practical ways to capture patterns of emotional harm, including text messages, school issues, and your child''s statements, in a court-friendly way.', 'documentation', 0, 0),
('Supporting Your Child When They''re Stuck in the Middle', 'Helping kids who feel torn, pressured to choose sides, or used as messengers between parents.', 'child-support', 0, 0),
('The Justice Gap: Why Court Doesn''t Punish Every Lie and What to Do Instead', 'Accepting that family court is about best interests, not moral justice, and shifting your strategy accordingly.', 'legal-reality', 0, 0),
('Creating a FU Binder (Litigation Log) That Actually Works in Court', 'Turning chaos into clarity with evidence logs, timelines, and neutral language that judges can quickly understand.', 'documentation', 0, 0),
('Talking to Your Lawyer So They Actually Understand the Pattern of Abuse', 'How to summarize a long, messy history into clear themes, incidents, and evidence your lawyer can use.', 'legal-strategy', 0, 0),
('Protecting Your Nervous System in a Never-Ending Custody Battle', 'Simple, realistic self-regulation tools for parents who feel constantly flooded, hypervigilant, or exhausted.', 'self-care', 0, 0),
('Setting Boundaries Around Last-Minute Change Requests', 'Scripts and strategies for responding when the other parent constantly asks to swap days or change pickups at the last second.', 'boundaries', 0, 0);
```

Click **Go**

Verify: Click **Browse** tab to see all 10 topics loaded

---

## Phase 7: Test First Post Generation

### Step 12: Manual Test Post

**In your browser:**

Navigate to:
```
https://custodybuddy.com/family-law-blog/cron.php?token=YOUR_32_CHAR_TOKEN
```

**Expected behavior:**
- Page loads (may take 30-60 seconds)
- Returns success message: "Blog post created successfully"
- Check homepage: New post should appear

**If error:**

Check PHP error logs:
1. Hostinger hPanel → **Advanced → Error Logs**
2. Find PHP errors related to your blog
3. Common issues:
   - Claude API key invalid
   - API timeout (increase max_execution_time)
   - Database write permissions
   - JSON parsing error

**Ask Cursor to debug:**
```
I'm getting this error when running cron.php:
[paste error message]

Please help me fix it. Remember this is on Hostinger shared hosting 
with MySQL database.
```

### Step 13: Verify Post Content

**Check the generated post:**

1. Navigate to homepage: `https://custodybuddy.com/family-law-blog/`
2. Click on the new post
3. Verify:
   - ✅ Title matches one of your 10 topics
   - ✅ Content is 800-1200 words
   - ✅ Tone is appropriate (validating, practical)
   - ✅ Includes actionable steps
   - ✅ 3 Amazon books at bottom
   - ✅ Affiliate links include your tag: `custodybuddy-20`
   - ✅ Legal/therapy disclaimers present
   - ✅ Mobile responsive design

**If content needs adjustment:**
```
Ask Cursor: "The tone is too [clinical/generic/etc]. Please update the 
content generation prompt to be more [validating/practical/specific]. 

Here's an example of the tone I want:
[paste example paragraph]
"
```

Then re-upload modified files to Hostinger.

---

## Phase 8: Automate with Hostinger Cron Jobs

### Step 14: Set Up Cron Job in hPanel

**In Hostinger hPanel:**

1. Navigate to **Advanced → Cron Jobs**
2. Click **Create Cron Job**

**Configure:**
```
Common Settings: Custom
Minute: 0
Hour: 9
Day: *
Month: *
Weekday: *

(This runs daily at 9:00 AM)

Command Type: URL
URL: https://custodybuddy.com/family-law-blog/cron.php?token=YOUR_32_CHAR_TOKEN

Email Notification: your@email.com (optional - notifies on success/fail)
```

**Alternative Schedules:**

**Twice daily (9 AM and 9 PM):**
```
Minute: 0
Hour: 9,21
Day: *
Month: *
Weekday: *
```

**Every 12 hours:**
```
Minute: 0
Hour: */12
Day: *
Month: *
Weekday: *
```

**For testing (every 5 minutes):**
```
Minute: */5
Hour: *
Day: *
Month: *
Weekday: *

⚠️ DISABLE THIS AFTER TESTING - Too frequent for production
```

3. Click **Create**

### Step 15: Test Cron Job

**Wait for scheduled time, or:**

**Manual trigger:**
1. In Cron Jobs list, find your job
2. Click **Run Now** (if available)
3. Or visit URL directly in browser

**Monitor:**
- Check email notifications (if enabled)
- Visit homepage to see if new post appeared
- Check hPanel → **Error Logs** for any cron-related errors

**Common Cron Issues:**

❌ **Timeout (30-second limit on shared hosting)**

Ask Cursor:
```
The cron job times out after 30 seconds but post generation takes longer. 
Please modify cron.php to:
1. Return immediately after starting post generation
2. Use background processing
3. Or split into smaller tasks

This is Hostinger shared hosting with typical limitations.
❌ Memory limit exceeded
Add to top of cron.php:
phpini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
```

❌ **Posts not generating**

Check:
- Token in URL matches config.php
- Claude API key is valid and has credit
- Database tables exist and are writable
- PHP error log shows specific issue

---

## Phase 9: Content Optimization (Hostinger-Specific)

### Step 16: Refine Content Generation

**Update prompts via file edit:**

1. In Hostinger File Manager, locate the file with Claude API prompts
   - Usually in `includes/content-generator.php` or similar
2. Click file → **Edit**
3. Modify the prompt text directly in browser
4. Click **Save**
5. Test with new manual cron trigger

**Or update locally in Cursor and re-upload:**
```
Ask Cursor: "Show me where the content generation prompts are stored. 
I want to refine the tone to be more [specific adjustment]."
```

Make changes in Cursor → Re-upload modified files to Hostinger

### Step 17: Add Your Custom Voice

**Provide example content:**

Create a new file locally: `voice-examples.txt`
```
TONE EXAMPLES FOR CO-PARENTING BLOG:

Example 1 - Validation + Action:
"You're not imagining it. When your co-parent ignores the parenting plan 
for the third time this month, it's not about 'miscommunication.' It's a 
pattern. And you're exhausted trying to figure out if you're overreacting.

Here's what's true: You can't control their behavior. The court won't 
punish every violation. And your kids are watching how you respond.

So what CAN you do? Document without obsessing. Respond without engaging. 
Protect without alienating. Let me show you how."

Example 2 - Realism + Hope:
"Family court isn't about who's right or wrong. It's about what's in the 
child's best interests. Yes, that means your ex might 'get away with' lying. 
Yes, that's infuriating.

But here's the shift: Stop fighting for justice. Start fighting for clarity. 
When you document patterns instead of incidents, use BIFF communication 
instead of emotional responses, and focus on your child's stability instead 
of exposing your ex... you win the long game."

Use this tone throughout all posts: validating, realistic, action-focused.
```

**Upload to Hostinger and update prompts:**
```
Ask Cursor: "Update the content generation system to use the tone and 
voice shown in voice-examples.txt. Apply this to all future posts."
```

Re-upload the modified code.

---

## Phase 10: Monitoring & Maintenance

### Step 18: Set Up Tracking

**Install Google Analytics:**

1. Create Google Analytics account
2. Get tracking code
3. In Hostinger File Manager:
   - Edit `includes/header.php`
   - Paste GA code before `</head>`
   - Save

**Or ask Cursor:**
```
Add Google Analytics tracking to the blog. My tracking ID is: G-XXXXXXXXXX

Make sure it's included on all pages.
```

### Step 19: Create Simple Analytics Dashboard

**Track in Google Sheets:**

Create sheet: "Blog Performance"

**Manual weekly check:**
- Posts published this week
- Top performing posts (GA)
- Affiliate clicks (Amazon Associates dashboard)
- Any errors in Hostinger logs

**Or automate:**

Ask Cursor:
```
Create a simple admin.php page that shows:
- Total posts published
- Posts by topic
- Most recent 10 posts
- Topics not yet used
- Simple statistics

This should be password protected. Use basic auth.
```

Upload admin.php to Hostinger.

Access: `https://custodybuddy.com/family-law-blog/admin.php`

### Step 20: Backup Strategy

**Hostinger includes backups, but:**

**Weekly manual backup (Recommended):**

1. Hostinger File Manager
2. Select `/public_html/family-law-blog/` folder
3. Right-click → **Compress**
4. Download ZIP file
5. Save locally with date: `custody-blog-backup-2024-12-01.zip`

**Database backup:**

1. phpMyAdmin → Select database
2. Click **Export**
3. Format: SQL
4. Click **Go**
5. Save: `custody-blog-db-2024-12-01.sql`

**Automated backups:**

Consider setting up GitHub:
1. Initialize git in local folder
2. Create GitHub repository
3. Push code regularly
4. Database backups can be automated with cron + mysqldump

---

## Phase 11: Advanced Features (Optional)

### Step 21: Add Email Signups

**Option A: Mailchimp Integration**

Ask Cursor:
```
Add a simple email signup form to the blog footer. 

Use Mailchimp's embedded form code. The form should:
- Ask for email only
- Have placeholder: "Get weekly co-parenting tips"
- Submit button: "Join Free"
- Match blog design
- Work on mobile

I'll provide the Mailchimp form action URL after generation.
```

Get form action URL from Mailchimp → Audience → Signup Forms → Embedded Forms

Update code, re-upload to Hostinger.

**Option B: Custom Email Collection**

Ask Cursor:
```
Create a simple email collection system that stores emails in MySQL.

Include:
- Form on blog footer
- subscribers table in database
- Simple double-opt-in
- Export to CSV function in admin.php
- GDPR-compliant (checkbox for consent)

No automated emails yet - just collection for now.
```

### Step 22: Add Lead Magnet

**Create downloadable resource:**

**Free FU Binder Template:**

1. Create PDF guide locally (use Canva, Google Docs, etc.)
2. Upload to Hostinger: `/public_html/family-law-blog/downloads/fu-binder-template.pdf`

**Gate it:**

Ask Cursor:
```
Create a lead magnet delivery system:

1. Add "Get Free FU Binder Template" button to homepage
2. Click → Modal popup with email form
3. After email submitted → Send download link
4. Store email in subscribers table

Make it simple - no external services needed yet.
```

This builds your email list while providing immediate value.

### Step 23: Add Social Sharing

**Easy social share buttons:**

Ask Cursor:
```
Add social sharing buttons to each blog post:

- Facebook
- Twitter/X
- LinkedIn
- Email (mailto link)
- Copy link button

Use simple URLs (no heavy JS libraries). Native share on mobile if supported.

Place below post title and at bottom of post.
```

---

## Troubleshooting Guide - Hostinger Specific

### Issue 1: Database Connection Fails

**Error:** "Could not connect to database"

**Check:**
```
1. Hostinger hPanel → Databases → MySQL Databases
2. Verify database exists: u123456789_custody_blog
3. Verify user has ALL PRIVILEGES
4. Confirm config.php has correct credentials
5. Try DB_HOST as 'localhost' AND your actual host (e.g., 'mysqlXX.hostinger.com')
Test database connection independently:
Create test-db.php:
php<?php
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>
Upload, visit: https://custodybuddy.com/family-law-blog/test-db.php
If successful, delete test-db.php for security.
Issue 2: Cron Job Not Running
Check Hostinger cron logs:

hPanel → Advanced → Cron Jobs
Find your job → View logs/output
Look for error messages

Common fixes:
❌ URL not accessible

Test URL manually in browser
Check token is correct
Verify no .htaccess blocking

❌ PHP execution time limit
Add to cron.php:
phpset_time_limit(300); // 5 minutes
❌ Memory exhausted
Add to cron.php:
phpini_set('memory_limit', '256M');
Issue 3: Posts Generate But Don't Display
Check:

Database → posts table → Verify posts exist
Check post status (should be "published" not "draft")
Verify index.php queries database correctly

Debug query:
Add to index.php temporarily:
phperror_reporting(E_ALL);
ini_set('display_errors', 1);

// After database query:
echo "Found " . $posts->num_rows . " posts<br>";
Visit homepage, check output.
Issue 4: Affiliate Links Don't Work
Verify:

Links format: https://www.amazon.com/dp/ASIN?tag=custodybuddy-20
Your Amazon Associate ID is approved and active
Links open in new tab: target="_blank"
No redirect or link cloaking (Amazon doesn't allow)

Test a link:

Click affiliate link
Check URL bar: Should include your tag
Make test purchase (or add to cart and check Amazon Associates dashboard)

Issue 5: "Permission Denied" Errors
Fix file permissions:

Hostinger File Manager
Select problem file/folder
Right-click → Permissions
Set:

Files: 644 (read/write for owner)
Folders: 755 (read/write/execute for owner)
Config.php: 644 (never 777!)


Apply

For upload directories (if exists):

/uploads/ folder: 755
Files inside: 644

Issue 6: Claude API Errors
"Rate limit exceeded":

You're making too many requests
Check cron isn't running too frequently
Verify you have API credits

"Invalid API key":

Check config.php has correct key
Verify no extra spaces or line breaks
Regenerate key in Claude console

"Context length exceeded":

Your prompt is too long
Ask Cursor to shorten the content generation prompt
DPContinue
Reduce max_tokens in API call


Cost Breakdown - Hostinger Version
Monthly Costs
Hostinger Hosting:

Plan: Premium or Business
Cost: $2.99-$4.99/month (with promo)
Includes: MySQL, cron jobs, SSL

Claude API:

Post generation: ~2,000 tokens/post
Book selection: ~800 tokens/post
Cost per post: ~$0.008
Daily posts: $0.24/month
Twice daily: $0.48/month

Domain (custodybuddy.com):

Cost: $10-15/year = $1.25/month

Total Monthly Cost:

Single daily post: ~$4-6/month
Twice daily posts: ~$5-7/month

Revenue Potential:

See earlier section on monetization
First month: Focus on building content
Month 3+: $30-100/month in affiliate revenue realistic
Month 6+: $100-500/month with email list + products


Deployment Checklist - Hostinger Specific
Pre-Launch

 MySQL database created in hPanel
 Database user created with all privileges
 config.php configured with all credentials
 All files uploaded to /public_html/family-law-blog/
 install.sql executed in phpMyAdmin
 10 topics loaded in database
 Test manual post generation (cron.php?token=...)
 Verify post displays correctly
 Check mobile responsive
 Affiliate links include correct tag

Launch Day

 Cron job configured in hPanel
 Scheduled for daily posting (9 AM recommended)
 Email notification enabled for cron
 Google Analytics installed
 Legal disclaimers on all posts
 Privacy policy page created
 Contact page/form created

Week 1 Monitoring

 Check daily that cron runs successfully
 Review each post for quality/tone
 Verify no PHP errors in logs
 Test affiliate links click through
 Check mobile display on actual devices
 Monitor Hostinger resource usage
 Backup database and files

Week 2+ Optimization

 Share first posts in Reddit communities
 Adjust tone based on feedback
 Add email signup form
 Create lead magnet (FU Binder template)
 Refine book selections
 Add more topics if needed
 Review analytics for top posts


Next Steps - Your Action Plan
This Week:
Day 1: Setup

 Install Cursor
 Create Claude API account
 Get Amazon Associate ID
 Create MySQL database in Hostinger

Day 2: Build

 Generate code with Cursor
 Configure config.php locally
 Test locally (optional)
 Upload to Hostinger File Manager

Day 3: Configure

 Run install.sql in phpMyAdmin
 Load 10 topics into database
 Test manual post generation
 Verify post displays correctly

Day 4: Automate

 Set up cron job in hPanel
 Test automated posting
 Refine tone/content if needed
 Install Google Analytics

Day 5: Launch

 Publish first real post
 Share in relevant communities
 Monitor for errors
 Create backup

Days 6-7: Iterate

 Review published posts
 Gather feedback
 Adjust prompts
 Plan email strategy

Month 1 Goals:

✅ 30 published posts
✅ Zero downtime / errors
✅ 100+ visitors from organic sharing
✅ 5+ affiliate link clicks
✅ Email signup form live

Month 2 Goals:

✅ 60 total posts (all topics cycled 2x)
✅ 500+ monthly visitors
✅ 50+ email subscribers
✅ First affiliate commission
✅ Lead magnet created and converting

Month 3+ Goals:

✅ 90+ total posts
✅ 2,000+ monthly visitors
✅ $50-100 affiliate revenue
✅ 200+ email subscribers
✅ Plan first info product


You're Ready to Launch
You have everything you need:
✅ Pre-researched topics (10 high-value topics)
✅ Hosting configured (Hostinger with domain)
✅ Clear deployment path (File Manager upload)
✅ Automation strategy (hPanel cron jobs)
✅ Monetization plan (Amazon affiliates)
✅ Content strategy (validated tone & structure)
The difference between you and someone who never launches:
You're going to execute in the next 7 days.
Start with Cursor today. Generate the code. Upload to Hostinger. Test one post. Then automate.
Your audience is waiting. 🚀
