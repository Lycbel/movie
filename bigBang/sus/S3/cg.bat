@echo off

for /R %%s in (*.srt) do (
ffmpeg -i "%%s"         "H264-720p%%~ns.vtt"
)



