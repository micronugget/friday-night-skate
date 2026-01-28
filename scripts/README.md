# Scripts Directory

This directory contains automation scripts for the Friday Night Skate Archive project.

## create_github_issues.py

Automatically creates GitHub Issues from the `.github/ISSUES.md` file.

### Purpose

This script parses the comprehensive ISSUES.md documentation and creates:
- 1 Epic Issue for the Archive Feature
- 15 Sub-Issues with proper labels, priorities, and linking

### Prerequisites

1. **GitHub CLI (gh) installed**
   ```bash
   # Check if installed
   which gh
   
   # Install on Ubuntu/Debian
   sudo apt install gh
   
   # Install on macOS
   brew install gh
   ```

2. **GitHub Authentication**
   ```bash
   gh auth login
   ```
   
   Follow the prompts to authenticate with GitHub.

### Usage

#### Dry Run (Recommended First)

Test what would be created without actually creating issues:

```bash
python3 scripts/create_github_issues.py --dry-run
```

This will show you:
- Which issues would be created
- Their titles and labels
- How they would be linked

#### Create Issues

Once you're satisfied with the dry run output:

```bash
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
- **Linking**: Sub-Issues linked to Epic via comment

### Labels Used

The script will apply these labels based on issue content:
- `epic` - Epic Issue
- `backend` - Backend development
- `frontend` - Frontend development
- `architecture` - Architectural changes
- `critical` - Critical priority
- `security` - Security-related
- `performance` - Performance optimization
- `documentation` - Documentation
- `devops` - DevOps/deployment

### Troubleshooting

#### "GitHub CLI (gh) not found"

Install the GitHub CLI:
- Ubuntu/Debian: `sudo apt install gh`
- macOS: `brew install gh`
- Windows: Download from https://cli.github.com/

#### "Not authenticated"

Run `gh auth login` and follow the prompts to authenticate.

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
