@echo off
REM Run Laravel scheduler once. Register this with Windows Task Scheduler to fire every minute.
REM Example one-time setup (run an elevated PowerShell):
REM   schtasks /Create /SC MINUTE /MO 1 /TN "Gesture\schedule-run" /TR "c:\xampp82\htdocs\task\gesture-schedule.bat" /RL HIGHEST /F
REM Remove with:
REM   schtasks /Delete /TN "Gesture\schedule-run" /F
cd /d "%~dp0"
"c:\xampp82\php\php.exe" artisan schedule:run >> storage\logs\schedule.log 2>&1
