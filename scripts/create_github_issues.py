#!/usr/bin/env python3
"""
Create GitHub Issues from .github/ISSUES.md

This script parses the comprehensive ISSUES.md file and creates GitHub Issues
for the Friday Night Skate Archive project using the GitHub CLI (gh).

Requirements:
- GitHub CLI (gh) installed
- Authenticated with GitHub: gh auth login
- Repository: micronugget/friday-night-skate

Usage:
    python3 scripts/create_github_issues.py [--dry-run]

Options:
    --dry-run    Show what would be created without actually creating issues
"""

import re
import subprocess
import sys
import json
from typing import Dict, List, Optional, Tuple
import argparse


class GitHubIssueCreator:
    """Parse ISSUES.md and create GitHub Issues"""
    
    def __init__(self, repo: str = "micronugget/friday-night-skate", dry_run: bool = False):
        self.repo = repo
        self.dry_run = dry_run
        self.epic_number = None
        self.issue_numbers = {}  # Map issue IDs to created issue numbers
        
    def check_gh_auth(self) -> bool:
        """Check if gh CLI is authenticated"""
        try:
            result = subprocess.run(
                ["gh", "auth", "status"],
                capture_output=True,
                text=True
            )
            return result.returncode == 0
        except FileNotFoundError:
            print("❌ Error: GitHub CLI (gh) not found. Please install it first.")
            print("   See: https://cli.github.com/")
            return False
    
    def parse_issues_file(self, filepath: str = ".github/ISSUES.md") -> Dict:
        """Parse ISSUES.md file and extract issue data"""
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Split by main sections
        sections = re.split(r'^## ', content, flags=re.MULTILINE)
        
        issues = {
            'epic': None,
            'sub_issues': []
        }
        
        for section in sections:
            if not section.strip():
                continue
            
            # Check if this is the Epic Issue
            if section.startswith('Epic Issue:'):
                issues['epic'] = self._parse_epic_issue(section)
            
            # Check if this is a Sub-Issue
            elif section.startswith('Sub-Issue #'):
                sub_issue = self._parse_sub_issue(section)
                if sub_issue:
                    issues['sub_issues'].append(sub_issue)
        
        return issues
    
    def _parse_epic_issue(self, section: str) -> Dict:
        """Parse Epic Issue section"""
        lines = section.split('\n')
        title_line = lines[0].replace('Epic Issue:', '').strip()
        
        # Extract title from the line
        title = title_line
        
        # Extract body content (everything after the title line)
        body = '\n'.join(lines[1:]).strip()
        
        # Extract labels from body
        labels = self._extract_labels(body)
        if 'epic' not in labels:
            labels.append('epic')
        
        return {
            'title': title,
            'body': body,
            'labels': labels
        }
    
    def _parse_sub_issue(self, section: str) -> Optional[Dict]:
        """Parse Sub-Issue section"""
        lines = section.split('\n')
        
        # Extract issue number and title from first line
        # Format: "Sub-Issue #1: Title"
        title_line = lines[0]
        match = re.match(r'Sub-Issue #(\d+):\s*(.+)', title_line)
        if not match:
            return None
        
        issue_id = match.group(1)
        title = match.group(2).strip()
        
        # Get the body (everything after title)
        body = '\n'.join(lines[1:]).strip()
        
        # Extract metadata
        labels = self._extract_labels(body)
        priority = self._extract_priority(body)
        
        return {
            'id': issue_id,
            'title': title,
            'body': body,
            'labels': labels,
            'priority': priority
        }
    
    def _extract_labels(self, text: str) -> List[str]:
        """Extract labels from issue body"""
        labels = []
        
        # Look for **Labels:** line
        label_match = re.search(r'\*\*Labels:\*\*\s*`([^`]+)`', text)
        if label_match:
            label_text = label_match.group(1)
            labels = [l.strip() for l in label_text.split(',')]
        
        return labels
    
    def _extract_priority(self, text: str) -> Optional[str]:
        """Extract priority from issue body"""
        priority_match = re.search(r'\*\*Priority:\*\*\s*(\w+)', text)
        if priority_match:
            return priority_match.group(1).lower()
        return None
    
    def create_issue(self, title: str, body: str, labels: List[str]) -> Optional[int]:
        """Create a GitHub issue using gh CLI"""
        
        if self.dry_run:
            print(f"\n📝 Would create issue: {title}")
            print(f"   Labels: {', '.join(labels)}")
            print(f"   Body length: {len(body)} chars")
            return 999  # Dummy issue number for dry run
        
        # Build gh issue create command
        cmd = [
            "gh", "issue", "create",
            "--repo", self.repo,
            "--title", title,
            "--body", body
        ]
        
        # Add labels
        for label in labels:
            cmd.extend(["--label", label])
        
        try:
            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                check=True
            )
            
            # Extract issue number from URL
            # Format: https://github.com/owner/repo/issues/123
            issue_url = result.stdout.strip()
            issue_number = int(issue_url.split('/')[-1])
            
            print(f"✅ Created issue #{issue_number}: {title}")
            return issue_number
            
        except subprocess.CalledProcessError as e:
            print(f"❌ Error creating issue '{title}': {e.stderr}")
            return None
    
    def link_to_epic(self, issue_number: int, epic_number: int):
        """Add comment linking sub-issue to epic"""
        if self.dry_run:
            print(f"   Would link #{issue_number} to Epic #{epic_number}")
            return
        
        comment = f"Part of Epic: #{epic_number}"
        
        cmd = [
            "gh", "issue", "comment", str(issue_number),
            "--repo", self.repo,
            "--body", comment
        ]
        
        try:
            subprocess.run(cmd, capture_output=True, check=True)
            print(f"   Linked to Epic #{epic_number}")
        except subprocess.CalledProcessError as e:
            print(f"   Warning: Could not link to epic: {e.stderr}")
    
    def create_all_issues(self) -> bool:
        """Create all issues from ISSUES.md"""
        
        # Check authentication
        if not self.dry_run and not self.check_gh_auth():
            print("\n❌ Please authenticate with GitHub first:")
            print("   gh auth login")
            return False
        
        print(f"\n{'🔍 DRY RUN MODE' if self.dry_run else '🚀 Creating GitHub Issues'}")
        print(f"Repository: {self.repo}\n")
        
        # Parse issues file
        print("📖 Parsing .github/ISSUES.md...")
        try:
            issues_data = self.parse_issues_file()
        except FileNotFoundError:
            print("❌ Error: .github/ISSUES.md not found")
            return False
        except Exception as e:
            print(f"❌ Error parsing ISSUES.md: {e}")
            return False
        
        # Create Epic Issue first
        if issues_data['epic']:
            print("\n📋 Creating Epic Issue...")
            epic = issues_data['epic']
            self.epic_number = self.create_issue(
                epic['title'],
                epic['body'],
                epic['labels']
            )
            
            if self.epic_number is None and not self.dry_run:
                print("❌ Failed to create Epic Issue. Aborting.")
                return False
        
        # Create Sub-Issues
        print(f"\n📋 Creating {len(issues_data['sub_issues'])} Sub-Issues...")
        
        for sub_issue in issues_data['sub_issues']:
            issue_number = self.create_issue(
                f"Sub-Issue #{sub_issue['id']}: {sub_issue['title']}",
                sub_issue['body'],
                sub_issue['labels']
            )
            
            if issue_number:
                self.issue_numbers[sub_issue['id']] = issue_number
                
                # Link to Epic
                if self.epic_number:
                    self.link_to_epic(issue_number, self.epic_number)
        
        # Summary
        print("\n" + "="*70)
        if self.dry_run:
            print("✅ DRY RUN COMPLETE")
            print(f"   Would create 1 Epic + {len(issues_data['sub_issues'])} Sub-Issues")
        else:
            print("✅ ISSUES CREATED SUCCESSFULLY")
            print(f"   Epic Issue: #{self.epic_number}")
            print(f"   Sub-Issues: {len(self.issue_numbers)} created")
            print(f"\nView issues at: https://github.com/{self.repo}/issues")
        print("="*70)
        
        return True


def main():
    """Main entry point"""
    parser = argparse.ArgumentParser(
        description='Create GitHub Issues from .github/ISSUES.md'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Show what would be created without actually creating issues'
    )
    parser.add_argument(
        '--repo',
        default='micronugget/friday-night-skate',
        help='GitHub repository (default: micronugget/friday-night-skate)'
    )
    
    args = parser.parse_args()
    
    creator = GitHubIssueCreator(repo=args.repo, dry_run=args.dry_run)
    success = creator.create_all_issues()
    
    sys.exit(0 if success else 1)


if __name__ == '__main__':
    main()
