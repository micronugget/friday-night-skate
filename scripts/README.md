# Scripts Directory

This directory contains automation scripts for the Friday Night Skate Archive project.

## create_github_issues.py

Automatically creates GitHub Issues from the `.github/ISSUES.md` file.

### Purpose

This script parses the comprehensive ISSUES.md documentation and creates:
- 1 Epic Issue for the Archive Feature
- 15 Sub-Issues with proper labels, priorities, and linking

### 🐳 Important for DDEV Users

**This script runs on your HOST machine (Ubuntu 24.04), NOT inside the DDEV container.**

Unlike Drupal commands which require `ddev` prefix, this script:
- ✅ Runs directly on your Ubuntu workstation
- ✅ Uses GitHub CLI installed on your host machine
- ✅ Does NOT use `ddev exec` or `ddev` prefix

**Why?** This script creates GitHub Issues via the GitHub API. It needs your personal GitHub authentication, which is handled on your host machine, not in the Docker container.

### Prerequisites

**All of these should be installed on your Ubuntu 24.04 HOST machine:**

1. **Python 3 (usually pre-installed on Ubuntu 24.04)**
   ```bash
   # Verify Python 3 is available on host
   python3 --version
   ```

2. **GitHub CLI (gh) - Install on HOST machine**
   ```bash
   # Check if already installed
   which gh
   
   # If not installed, install on Ubuntu 24.04:
   (type -p wget >/dev/null || (sudo apt update && sudo apt-get install wget -y)) \
   && sudo mkdir -p -m 755 /etc/apt/keyrings \
   && wget -qO- https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo tee /etc/apt/keyrings/githubcli-archive-keyring.gpg > /dev/null \
   && sudo chmod go+r /etc/apt/keyrings/githubcli-archive-keyring.gpg \
   && echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
   && sudo apt update \
   && sudo apt install gh -y
   ```

3. **GitHub Authentication - On HOST machine**
   ```bash
   # Authenticate with GitHub (on your Ubuntu host, NOT in DDEV)
   gh auth login
   ```
   
   Follow the prompts:
   - Choose: **GitHub.com**
   - Choose: **HTTPS** (recommended) or SSH
   - Choose: **Login with a web browser** (easiest)
   - Copy the one-time code shown
   - Press Enter to open browser
   - Paste the code and authorize
   
   Once authenticated, verify with:
   ```bash
   gh auth status
   ```

### Usage

**⚠️ Run these commands on your Ubuntu HOST machine, NOT in DDEV:**

#### Step 1: Authenticate (One-Time Setup)

```bash
# On your Ubuntu 24.04 host machine:
gh auth login
```

Follow the browser authentication flow.

#### Step 2: Dry Run (Recommended First)

Test what would be created without actually creating issues:

```bash
# On your Ubuntu 24.04 host machine:
python3 scripts/create_github_issues.py --dry-run
```

This will show you:
- Which issues would be created
- Their titles and labels
- How they would be linked

#### Step 3: Create Issues

Once you're satisfied with the dry run output:

```bash
# On your Ubuntu 24.04 host machine:
python3 scripts/create_github_issues.py
```

This will:
1. ✅ Check GitHub authentication
2. 📖 Parse `.github/ISSUES.md`
3. 📋 Create Epic Issue first
4. 📋 Create 15 Sub-Issues
5. 🔗 Link each Sub-Issue to the Epic
6. 🏷️ Apply appropriate labels

#### Custom Repository

If you need to create issues in a different repository:

```bash
python3 scripts/create_github_issues.py --repo owner/repo-name
```

### Output Example

```
🚀 Creating GitHub Issues
Repository: micronugget/friday-night-skate

📖 Parsing .github/ISSUES.md...

🏷️  Creating 42 missing labels...
   ✅ Created label: epic
   ✅ Created label: backend
   ✅ Created label: frontend
   ... (labels created automatically)

📋 Creating Epic Issue...
✅ Created issue #1: Friday Night Skate Archive Feature

📋 Creating 15 Sub-Issues...
✅ Created issue #2: Sub-Issue #1: Taxonomy & Content Architecture
   Linked to Epic #1
✅ Created issue #3: Sub-Issue #2: Media & Metadata Extraction Service
   Linked to Epic #1
...

======================================================================
✅ ISSUES CREATED SUCCESSFULLY
   Epic Issue: #1
   Sub-Issues: 15 created

View issues at: https://github.com/micronugget/friday-night-skate/issues
======================================================================
```

### What Gets Created

Each issue includes:
- **Title**: Descriptive title from ISSUES.md
- **Body**: Complete technical specification including:
  - Description
  - Requirements
  - Technical Tasks (checklist)
  - Validation procedures
  - Dependencies
  - Handoff information
- **Labels**: e.g., `backend`, `frontend`, `critical`, `security`
  - **Automatically created** if they don't exist in the repository
- **Linking**: Sub-Issues linked to Epic via comment

### Labels Automatically Created

The script automatically creates any missing labels before creating issues. Labels include:
- `epic` - Epic Issue
- `backend` - Backend development
- `frontend` - Frontend development
- `architecture` - Architectural changes
- `critical` - Critical priority
- `security` - Security-related
- `performance` - Performance optimization
- `documentation` - Documentation
- `devops` - DevOps/deployment
- Plus many more (see script for full list)

**Note:** Labels are created with predefined colors for consistency across the project.

### Troubleshooting

#### 🐳 "I'm using DDEV - where do I run these commands?"

**Run the script on your Ubuntu 24.04 HOST machine, NOT inside DDEV.**

```bash
# ❌ DON'T use ddev exec for this script
# ❌ ddev exec python3 scripts/create_github_issues.py

# ✅ DO run directly on your Ubuntu host
python3 scripts/create_github_issues.py --dry-run
```

**Why?** 
- This script uses GitHub CLI (`gh`) which needs your personal GitHub authentication
- GitHub authentication is handled on your host machine, not in the Docker container
- Unlike Drupal commands (which need `ddev` prefix), this script manages GitHub Issues via the API

**Your workflow:**
1. Use DDEV for Drupal development: `ddev drush`, `ddev composer`, etc.
2. Use host machine for GitHub Issues: `gh auth login`, `python3 scripts/create_github_issues.py`

#### "GitHub CLI (gh) not found" on Ubuntu 24.04

Install the GitHub CLI on your **host machine**:

```bash
# Official installation for Ubuntu 24.04
(type -p wget >/dev/null || (sudo apt update && sudo apt-get install wget -y)) \
&& sudo mkdir -p -m 755 /etc/apt/keyrings \
&& wget -qO- https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo tee /etc/apt/keyrings/githubcli-archive-keyring.gpg > /dev/null \
&& sudo chmod go+r /etc/apt/keyrings/githubcli-archive-keyring.gpg \
&& echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
&& sudo apt update \
&& sudo apt install gh -y
```

Then authenticate:
```bash
gh auth login
```

#### "Not authenticated"

Run `gh auth login` on your **host machine** (not in DDEV) and follow the browser authentication flow:
1. Choose: GitHub.com
2. Choose: HTTPS (recommended)
3. Choose: Login with a web browser
4. Copy the one-time code
5. Complete authentication in browser

Verify authentication:
```bash
gh auth status
```

#### "gh auth login doesn't work in my terminal"

If you're having issues with the browser flow:
1. Use the **token** authentication method instead
2. Generate a personal access token at: https://github.com/settings/tokens
3. Select scopes: `repo` (full repository access)
4. When running `gh auth login`, choose "Paste an authentication token"

#### "Permission denied"

Make sure you have permission to create issues in the repository. You need at least "Write" access.

#### Issues not linking properly

The script adds a comment linking to the Epic. If this fails, you can manually link them by:
1. Editing each Sub-Issue
2. Adding "Part of Epic: #X" to the description or a comment

### Re-running the Script

⚠️ **Warning**: Running the script multiple times will create duplicate issues. There is no de-duplication logic.

If you need to recreate issues:
1. Close all existing issues first
2. Or manually delete them
3. Then re-run the script

### Manual Alternative

If you prefer to create issues manually, you can copy-paste from `.github/ISSUES.md`:
1. Copy the Epic Issue section
2. Create a new issue in GitHub
3. Paste the content
4. Add labels
5. Repeat for each Sub-Issue

---

## Other Scripts

(Future scripts will be documented here)
