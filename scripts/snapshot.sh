#!/usr/bin/env bash
set -euo pipefail

# آخرین تگ step-* را از ساب‌ماژول بگیر (گولیتیم)
git -C wp-content fetch --tags origin || true
LAST_STEP=$(git -C wp-content tag --list 'step-*' --sort=creatordate | tail -n1 || true)
if [ -z "${LAST_STEP:-}" ]; then
  echo "❌ No step-* tags found in wp-content. Tag a step first."
  exit 1
fi

# ساب‌ماژول را به آخرین commit شاخهٔ تنظیم‌شده (golitheme) به‌روزرسانی کن
git submodule update --remote wp-content

# اسنپ‌شات با همان نام مرحله (backup-<step-...>)
git add wp-content
git commit -m "chore(backup): snapshot ${LAST_STEP} (bump wp-content)"
git tag -a "backup-${LAST_STEP}" -m "Backup for ${LAST_STEP}"
git push && git push --tags

echo "✅ Snapshot recorded: backup-${LAST_STEP}"
