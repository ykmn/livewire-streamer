livewire-streamer
=================
This is a simple Ubuntu upstart script that runs some utilities to pipe audio from a Livewire network 
to an instance of Icecast.

* Livewire is a proprietary branch of AES69 protocol used in the broadcast industry to stream CD quality audio over a network.


Setup
=====
You need a small Ubuntu VM on your Livewire network, I used 16.04, you can probably get away with 1 core if you run server edition. Suppose your current user is `axia`.

* Configure multicast on your Livewire network interface. Suppose it's `ens33`:
`sudo ifconfig ens33 multicast`

* Don't forget to add route of multicast traffic to your Livewire interface if you have more than one network interface:
`sudo route add -net 224.0.0.0 netmask 240.0.0.0 dev ens33` or add route to `/etc/network/interfaces`:
`auto ens33
iface ens33 inet static
        address 172.22.0.100
        netmask 255.255.0.0
        network 172.22.0.0
        broadcast 172.22.255.255
        up route add -net 224.0.0.0 netmask 240.0.0.0 dev ens33`
and restart interface: `sudo ifdown ens33 && ifup ens33`

* Do Linux Update: `sudo apt update && sudo apt upgrade -y`

* Install AVconv, dumpRTP and ezStream: `sudo apt install -y libav-tools ezstream dvbstream`
- `libav-tools` for avconv utility, make sure it's installed with MP3 support
- `ezstream` for streaming to icecast
- `dvbstream` for the dumptrp utility

* Copy `stream.sh` and `stream-restart.sh` to user's home folder: `/home/axia/`
- Set permissions for www-data: `sudo chown www-data:www-data /home/axia/stream*.sh && sudo chmod +x /home/axia/stream*.sh`

* Edit `ezstream.xml` for your Icecast connecting.
- Copy `ezstream.xml` to `/home/axia/`
- Permissions for `ezstream.xml` are axia:axia and 644.

* Create `stream` service for systemd:
- put `stream.service` to `/etc/systemd/system/`
- `sudo chmod 644 /etc/systemd/system/stream.service`
- `sudo chown root:root /etc/systemd/system/stream.service`

#### Service management:
- Reload systemd: `sudo systemctl daemon-reload`
- Enable and start service: `sudo systemctl enable stream && sudo systemctl start stream`
- Check service status: `sudo systemctl status stream` or `sudo journalctl -u stream`
- Edit service configuration: `sudo systemctl edit --full stream`
Notice that service is running from `www-data` user (User=www-data).

* Install Apache2 and PHP: `sudo apt install -y apache2 php`

* Edit `axia.php`:
- find string `<a target="_blank" href="http://YOUR-ICECAST-SERVER:8000/axia">PLAY</a>` and replace address of stream accordingly `ezstream.xml` settings

* Copy `axia.php` to /var/www/html

* Open http://yout-ubuntu-host/axia.php in browser, input Livewire channel, click "Write config to File" and click PLAY link.

Configuration
=============
#### Output formatting
* `avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K -` This portion of the command transcodes the audio 
  * `-f mp3` Change that to the format you want to stream / output, Check avconv -formats for output codecs
  * `-b:a 320K` Change 320K to the bit rate you want to stream

Troubleshooting
===============
* If the stream upstart job doesn't stay running check /var/log/upstart/stream for errors
