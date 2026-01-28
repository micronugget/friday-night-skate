# Quick Start Guide for DDEV Users on Ubuntu 24.04

This guide is specifically for users running DDEV on Ubuntu 24.04 workstations.

## Understanding Host vs Container

When using DDEV, you work in two different environments:

### 🐳 Inside DDEV Container
Use `ddev` prefix for Drupal commands:
```bash
ddev drush cr              # Clear Drupal cache
ddev composer require      # Install Drupal modules
ddev phpunit              # Run PHP tests
ddev exec ffprobe         # Run ffprobe for metadata
```

### 💻 On Ubuntu Host Machine
Run directly for GitHub operations:
```bash
gh auth login                              # Authenticate GitHub CLI
python3 scripts/create_github_issues.py   # Create GitHub Issues
git status                                 # Git commands
```

## Setting Up GitHub CLI (One-Time)

### Step 1: Install GitHub CLI on Your Ubuntu 24.04 Host

Open a terminal on your Ubuntu host (NOT in DDEV) and run:

```bash
# Install GitHub CLI
(type -p wget >/dev/null || (sudo apt update && sudo apt-get install wget -y)) \
&& sudo mkdir -p -m 755 /etc/apt/keyrings \
&& wget -qO- https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo tee /etc/apt/keyrings/githubcli-archive-keyring.gpg > /dev/null \
&& sudo chmod go+r /etc/apt/keyrings/githubcli-archive-keyring.gpg \
&& echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
&& sudo apt update \
&& sudo apt install gh -y
```

Verify installation:
```bash
gh --version
```

### Step 2: Authenticate with GitHub

Still on your Ubuntu host:

```bash
gh auth login
```

Follow the prompts:
1. **What account do you want to log into?** → GitHub.com
2. **What is your preferred protocol?** → HTTPS (recommended)
3. **How would you like to authenticate?** → Login with a web browser
4. **Copy the code:** → Press Enter to open browser
5. **In the browser:** Paste the code and authorize

Verify authentication:
```bash
gh auth status
```

You should see:
```
✓ Logged in to github.com as YOUR_USERNAME
```

## Creating GitHub Issues

Now you can create the issues:

```bash
# Navigate to your repository
cd /path/to/friday-night-skate

# Test first (dry run)
python3 scripts/create_github_issues.py --dry-run

# Create all 16 issues
python3 scripts/create_github_issues.py
```

## Common Issues

### "gh: command not found"
- You're probably inside DDEV
- Exit DDEV and run on your Ubuntu host

### "gh auth login" doesn't respond
- Make sure you're on your Ubuntu host, not in DDEV
- Try the token method instead (see scripts/README.md)

### "Permission denied"
- Make sure you have write access to the repository
- Check: `gh auth status`

## Daily Workflow

Here's how to work with both environments:

```bash
# 1. Start your day - start DDEV
ddev start

# 2. Drupal development - use DDEV
ddev drush cr
ddev composer require drupal/some-module
ddev phpunit

# 3. GitHub Issues - use HOST
python3 scripts/create_github_issues.py --dry-run

# 4. Git operations - use HOST
git status
git add .
git commit -m "Your message"
git push

# 5. End of day - stop DDEV
ddev stop
```

## Need More Help?

- **DDEV Documentation:** https://ddev.readthedocs.io/
- **GitHub CLI Documentation:** https://cli.github.com/manual/
- **Script Details:** See [scripts/README.md](scripts/README.md)
- **Project Issues:** See [.github/ISSUES.md](.github/ISSUES.md)
