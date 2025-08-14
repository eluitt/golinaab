#!/usr/bin/env bash
set -euo pipefail

# ------------------------------------------------------------------------------
# Snapshot wp-content submodule pointer to the latest step-* tag (pin to tag)
# Creates a root tag: backup-<step-tag>[-YYYYmmdd-HHMMSS] and pushes.
# Usage: bash scripts/snapshot.sh [mode]
#   mode: "tag" (default) => pin to latest step-* tag
#         "head"          => pin to remote branch head from .gitmodules (ff-only)
# ------------------------------------------------------------------------------

MODE="${1:-tag}"             # "tag" or "head"
SUB_PATH="wp-content"

# Ensure we run from the repository root
TOP="$(git rev-parse --show-toplevel 2>/dev/null || true)"
if [ -z "$TOP" ]; then
  echo "❌ Not inside a git repository."; exit 1
fi
cd "$TOP"

# Basic sanity
if [ ! -d "$SUB_PATH" ]; then
  echo "❌ Missing submodule directory: $SUB_PATH"
  exit 1
fi

# Refuse to proceed if there are uncommitted changes in submodule or root
if [ -n "$(git -C "$SUB_PATH" status --porcelain)" ]; then
  echo "❌ Submodule has uncommitted changes in $SUB_PATH. Commit or stash first."
  git -C "$SUB_PATH" status --porcelain
  exit 1
fi
if [ -n "$(git status --porcelain)" ]; then
  echo "❌ Root repo has uncommitted changes. Commit or stash first."
  git status --porcelain
  exit 1
fi

# Make sure submodule is initialized and up-to-date with remote metadata
git submodule update --init --recursive
git -C "$SUB_PATH" fetch --tags --prune origin || true

# Determine target commit for submodule pointer
TARGET_DESC=""
TARGET_SHA=""

if [ "$MODE" = "head" ]; then
  # Pin to remote branch head from .gitmodules (fallback to 'golitheme' then 'main')
  BRANCH="$(git config -f .gitmodules submodule.$SUB_PATH.branch || echo golitheme)"
  git -C "$SUB_PAT_
