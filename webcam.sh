#!/bin/sh
export WEBCAM_FILENAME=`ls -r -d /var/www/html/webcam/* | head -1`"/"`date +%Y%m%d%H%M%S`".jpg"
ffmpeg -loglevel error -err_detect aggressive -fflags discardcorrupt -noaccurate_seek -discard nokey -rtsp_transport -udp_multicast -i rtsp://admin:beer559317@10.0.0.15/onvif1 -ss 10 -frames 1 -pix_fmt gray -vf format=gray,scale=640:480 -qscale:v 3 $WEBCAM_FILENAME
chmod 0666 $WEBCAM_FILENAME
