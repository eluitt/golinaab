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
  git -C "$SUB_PATH" fetch origin "$BRANCH" --prune
  TARGET_DESC="branch-$BRANCH"
  TARGET_SHA="$(git -C "$SUB_PATH" rev-parse "origin/$BRANCH")"
  # Ensure fast-forward only (no history rewrite)
  git -C "$SUB_PATH" checkout --detach "$TARGET_SHA"
else
  # MODE=tag (default): Pin to latest step-* tag by creation date
  LAST_STEP="$(git -C "$SUB_PATH" for-each-ref --sort=creatordate --format='%(refname:short)' refs/tags/step-* | tail -n1 || true)"
  if [ -z "${LAST_STEP:-}" ]; then
    echo "❌ No step-* tags found in $SUB_PATH. Tag a step first (e.g., step-00-tooling)."
    exit 1
  fi
  TARGET_DESC="$LAST_STEP"
  TARGET_SHA="$(git -C "$SUB_PATH" rev-parse "$LAST_STEP")"
  git -C "$SUB_PATH" checkout --detach "$TARGET_SHA"
fi

SHORT_SHA="$(git -C "$SUB_PATH" rev-parse --short "$TARGET_SHA")"

# Stage submodule pointer change in root (if any)
git add "$SUB_PATH"
if git diff --cached --quiet -- "$SUB_PATH"; then
  echo "ℹ️ No submodule pointer change to commit (already at $TARGET_DESC @$SHORT_SHA)."
else
  git commit -m "chore(backup): snapshot $TARGET_DESC @ $SHORT_SHA (pin $SUB_PATH)"
fi

# Create root tag
TAG_BASE="backup-$TARGET_DESC"
TAG_NAME="$TAG_BASE"
if git rev-parse -q --verify "refs/tags/$TAG_NAME" >/dev/null; then
  # Avoid collision by appending timestamp
  TS="$(date +%Y%m%d-%H%M%S)"
  TAG_NAME="$TAG_BASE-$TS"
fi
git tag -a "$TAG_NAME" -m "Backup snapshot for $TARGET_DESC @ $SHORT_SHA"

# Push root commits and tags
git push
git push origin --tags

echo "✅ Snapshot recorded: $TAG_NAME (-> $TARGET_DESC @$SHORT_SHA)"
