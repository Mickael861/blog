@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../spryker/code-sniffer/bin/tokenize
sh "%BIN_TARGET%" %*
