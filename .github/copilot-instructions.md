# GitHub Copilot Mission Control — Install & Setup

This guide explains how to enable and access GitHub Copilot Mission Control, and how to set up IDE clients so Mission Control policies apply to developers.

## What Is Mission Control?
Mission Control is the admin control plane for GitHub Copilot in GitHub Enterprise Cloud. It provides organization- and enterprise-level policy management, usage analytics, and governance. It is not a local VS Code extension; access is via GitHub.com and requires appropriate admin permissions.

## Prerequisites
- GitHub Enterprise Cloud account.
- Copilot plan enabled for your enterprise/org (Copilot Enterprise recommended).
- Enterprise owner or delegated admin permissions to configure Copilot.

## Enable Copilot and Access Mission Control
1. Sign in to GitHub.com with an enterprise owner or delegated admin account.
2. Go to your Enterprise account: Settings → Copilot.
3. Enable “Allow GitHub Copilot for this enterprise”. Choose seat management (self-serve or admin assignment).
4. Open Copilot → Mission Control in the left navigation to access dashboards and policy configuration.

## Configure Policies
- Data controls: allow/deny public code references and telemetry.
- Models & features: choose default suggestion model (if available) and feature toggles.
- Allow/Deny lists: restrict Copilot to specific orgs, repos, or groups.
- Compliance: export usage, set retention, and apply SSO/SAML requirements.

## IDE Setup (Developers)
Developers must install Copilot clients; Mission Control policies apply automatically once signed in with enterprise accounts.

### VS Code (Linux/macOS/Windows)
```bash
code --install-extension GitHub.copilot
code --install-extension GitHub.copilot-chat
```

Then:
- Open VS Code → sign in to GitHub (Accounts menu or the Copilot sign-in prompt).
- Ensure you’re using your enterprise GitHub identity (SSO/SAML if required).

### JetBrains
- Install “GitHub Copilot” plugin from Marketplace.
- Sign in to GitHub with your enterprise account.

## Verify Mission Control is Active
- In GitHub.com → Mission Control, check “Users” and “Usage” dashboards for active seats.
- In VS Code, ensure Copilot is enabled (status icon) and suggestions appear; policy restrictions (e.g., public code references) should reflect admin settings.

## Common Issues
- Mission Control not visible: ensure you’re on GitHub Enterprise Cloud and are an enterprise owner or delegated admin; Copilot must be enabled for the enterprise.
- Seat assignment: confirm orgs are included and users have seats.
- On-prem (GitHub Enterprise Server): Mission Control is a Cloud feature.
- Network/SSO: allow GitHub endpoints and complete SSO/SAML linking.

## Helpful Links
- GitHub Docs → Copilot for Enterprise (Mission Control, policies, analytics).
- GitHub Docs → Copilot in VS Code and JetBrains.

