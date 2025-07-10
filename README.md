livewire-streamer
=================
This is a simple set of Ubuntu scripts that runs some utilities to pipe audio from a Livewire network 
to an instance of Icecast.

* Livewire is a proprietary branch of AES69 protocol used in the broadcast industry to stream CD quality audio over a network.


Setup
=====
You need a small Ubuntu VM on your Livewire network, I used 16.04, you can probably get away with 1 core if you run server edition. Suppose your current user is `axia`.

1. Configure multicast on your Livewire network interface. Suppose it's `ens33`:
`sudo ifconfig ens33 multicast`

2. Don't forget to add route of multicast traffic to your Livewire interface if you have more than one network interface:
`sudo route add -net 224.0.0.0 netmask 240.0.0.0 dev ens33` or add route to `/etc/network/interfaces`:
```
auto ens33
iface ens33 inet static
        address 172.22.0.100
        netmask 255.255.0.0
        network 172.22.0.0
        broadcast 172.22.255.255
        up route add -net 224.0.0.0 netmask 240.0.0.0 dev ens33
```
and restart interface: `sudo ifdown ens33 && ifup ens33`

3. Do Linux Update: `sudo apt update && sudo apt upgrade -y`

4. Install AVconv, dumpRTP and ezStream: `sudo apt install -y libav-tools ezstream dvbstream`
- `libav-tools` for avconv utility, make sure it's installed with MP3 support
- `ezstream` for streaming to icecast
- `dvbstream` for the dumptrp utility

5. Copy `stream.sh` and `stream-restart.sh` to user's home folder: `/home/axia/`
- Set permissions for www-data: `sudo chown www-data:www-data /home/axia/stream*.sh && sudo chmod +x /home/axia/stream*.sh`

6. Edit `ezstream.xml` for your Icecast connecting.
- Copy `ezstream.xml` to `/home/axia/`
- Permissions for `ezstream.xml` are axia:axia and 644.

7. Create `stream` service for systemd:
- put `stream.service` to `/etc/systemd/system/`
- `sudo chmod 644 /etc/systemd/system/stream.service`
- `sudo chown root:root /etc/systemd/system/stream.service`

#### Service management:
- Reload systemd: `sudo systemctl daemon-reload`
- Enable and start service: `sudo systemctl enable stream && sudo systemctl start stream`
- Check service status: `sudo systemctl status stream` or `sudo journalctl -u stream`
- Edit service configuration: `sudo systemctl edit --full stream`
Notice that service is running from `www-data` user (User=www-data).

8. Install Apache2 and PHP: `sudo apt install -y apache2 php`

9. Edit `index.php`:
- find strings `http://YOUR-ICECAST-SERVER:8000/axia` and replace this address of stream accordingly `ezstream.xml` settings

10. Copy `index.php` to /var/www/html

11. Open http://your-ubuntu-host/index.php in browser, input Livewire channel, click "Write config to File" and click PLAY link.

Configuration
=============
#### Output formatting
* `avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K -` This portion of the command transcodes the audio 
  * `-f mp3` Change that to the format you want to stream / output, Check avconv -formats for output codecs
  * `-b:a 320K` Change 320K to the bit rate you want to stream

Preview
=============
![Web page preview](index.png)

Troubleshooting
===============
- Check service status: `sudo systemctl status stream` or `sudo journalctl -u stream`
