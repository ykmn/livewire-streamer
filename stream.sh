/usr/bin/dumprtp 239.192.8.253 5004 | /usr/bin/avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K - | /usr/bin/ezstream -c /home/axia/ezstream.xml
