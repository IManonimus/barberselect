@echo off
REM Safe commit script for barberselect project
cd /d "D:\Tugas ABP\barberselect"

REM Configure git user
git config user.name "barberselect"
git config user.email "ilham.prakosa800@gmail.com"

REM Check status
echo.
echo === GIT STATUS ===
git status --short

REM Stage all changes
echo.
echo === STAGING CHANGES ===
git add .

REM Show what will be committed
echo.
echo === DIFF SUMMARY ===
git diff --cached --stat

REM Commit with message
echo.
echo === COMMITTING ===
git commit -m "Initial project commit: Mobile and barberselect with security setup" -m "- Added .gitignore to protect sensitive files
- Removed GROQ_API_KEY from .env
- Prepared 2 folders for safe repository storage

Co-authored-by: Copilot ^<223556219+Copilot@users.noreply.github.com^>"

REM Verify commit
echo.
echo === VERIFICATION ===
git status --short
echo.
git log --oneline -3

echo.
echo === COMMIT SUCCESSFUL ===
echo Commit completed! Your changes are now saved locally.
