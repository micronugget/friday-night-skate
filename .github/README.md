# Copilot Setup & Instructions

This directory contains important files for GitHub Copilot agents working on this repository.

## 🚨 Start Here

### For Copilot Agents

**BEFORE starting any work, you MUST run:**

```bash
bash .github/copilot-setup.sh
```

This sets up the DDEV environment that is **required** for all development work on this project.

## Files in This Directory

### Setup & Configuration

- **`copilot-setup.sh`** - **⭐ RUN THIS FIRST** - Automated setup script that installs DDEV and configures the development environment
- **`copilot-setup-steps.yml`** - GitHub Actions workflow defining all setup steps (used as basis for the setup script)
- **`copilot-instructions.md`** - **⭐ READ THIS** - Project standards, coding guidelines, and technical requirements
- **`check-setup.sh`** - Quick verification script to check if environment is ready

### Specialized Agents

- **`AGENT_DIRECTORY.md`** - **⭐ AGENT QUICK REFERENCE** - Complete guide to all specialized agents, when to use them, and task matching
- **`agents/`** - Directory containing 15+ specialized agent configurations:
  - **Core Development**: Drupal Developer, Media Developer, Themer
  - **Quality & Docs**: Tester, Technical Writer
  - **Infrastructure**: Environment Manager, Provisioner/Deployer, Ansible Developer
  - **Specialists**: Security Specialist, Performance Engineer, Database Administrator
  - **Planning**: Architect (Mission Control)
  - **Design**: UX/UI Designer
  - See `AGENT_DIRECTORY.md` for complete list with decision tree

### Documentation

- **`SETUP_AUTOMATION.md`** - Documentation of the setup automation solution

### Issues

- **`ISSUES.md`** - List of project issues and tasks
- **`ISSUES_SUMMARY.md`** - Summary of issue status and priorities

## Quick Start Workflow

1. **Setup Environment** (one-time or when starting fresh):
   ```bash
   bash .github/copilot-setup.sh
   ```

2. **Read Project Instructions**:
   ```bash
   cat .github/copilot-instructions.md
   ```

3. **Check Available Specialized Agents**:
   ```bash
   cat .github/AGENT_DIRECTORY.md
   ```

4. **Identify Right Agent for Your Task**:
   - View agent directory for task matching
   - Use decision tree in AGENT_DIRECTORY.md
   - Delegate to specialized agent when available

5. **Verify Setup**:
   ```bash
   ddev --version
   ddev describe
   ddev drush status
   ```

6. **Start Working**:
   - All Drush commands: `ddev drush [command]`
   - All Composer commands: `ddev composer [command]`
   - All PHPUnit tests: `ddev phpunit [options]`

## Why This Matters

This project uses **DDEV** for local development. Without it:
- ❌ You cannot run Drush commands
- ❌ You cannot run Composer
- ❌ You cannot run tests
- ❌ You cannot validate your changes

The setup script ensures you have a working environment before you start coding.

## Troubleshooting

### "ddev: command not found"

Run the setup script:
```bash
bash .github/copilot-setup.sh
```

### "DDEV project not configured"

The setup script should handle this, but if it fails:
```bash
ddev config --project-type=drupal11 --docroot=web --project-name=friday-night-skate
ddev start
```

### Setup script fails

Check the detailed steps in `copilot-setup-steps.yml` and run them manually.

## Need Help?

- **Project README**: `/README.md`
- **DDEV Quick Start**: `/QUICKSTART_DDEV.md`
- **Copilot Instructions**: `.github/copilot-instructions.md`
- **DDEV Documentation**: https://ddev.readthedocs.io/
