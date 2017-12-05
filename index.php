<html>
<head>
<meta charset="utf-8">
<title>Axia player and SDP generator</title>
<style>
body {
	margin-left: 50px;
	margin-right: 25px;
	background: #e0e3e7;
	width:80%;
	margin:0px auto;
	background-attachment: fixed;
}
body, td {
	font-family: Cuprum, "Segoe UI", tahoma;
	font-size: 14px;
}
input {
	font-family: Cuprum, "Segoe UI", tahoma;
	font-size: 14px;
	color: #000;
	background: #ccc;
}

</style>
<script type='text/javascript' src='//code.jquery.com/jquery-1.10.1.js'></script>
<script type="text/javascript">

function mksdp(srcnode, chan) {
    if(parseInt($("chan").val()) > 32385) { return }

    var b2, b3, mcastip, sdp;
    b2 = Math.floor(chan / 256);
    b3 = chan - b2 * 256;
    mcastip = "239.192." + b2 + "." + b3;
   
    sdp = "v=0\n";
    sdp += "o=Node 1 1 IN IP4 " + srcnode + "\n";
    sdp += "s=TestSine" + "\n";
    sdp += "t=0 0" + "\n";
    sdp += "a=type:multicast" + "\n";
    sdp += "c=IN IP4 " + mcastip + "\n";
    sdp += "m=audio 5004 RTP/AVP 97" + "\n";
    sdp += "a=rtpmap:97 L24/48000/2" + "\n";
    return sdp;
}

function mkcfg(srcnode, chan) {
    if(parseInt($("chan").val()) > 32385) { return }

    var b2, b3, mcastip, content;
    b2 = Math.floor(chan / 256);
    b3 = chan - b2 * 256;
    mcastip = "239.192." + b2 + "." + b3;
//    content = "# Upstart file at /etc/init/stream.conf" + "\n";
//    content += "respawn" + "\n";
//    content += "respawn limit 1 5" + "\n";
//    content += "post-stop exec sleep 5" + "\n\n";
//    content += "start on net-device-up IFACE=eth0" + "\n";
//    content += "stop on net-device-down IFACE=eth0" + "\n";
//    content += "script" + "\n";
//    content += "dumprtp " + mcastip + " 5004 \| avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K - \| ezstream -c /etc/ezstream.xml" + "\n";
//    content += "end script" + "\n";

	content = "/usr/bin/dumprtp " + mcastip + " 5004 \| avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K - \| ezstream -c /home/axia/ezstream.xml";
//      content += "dumprtp " + mcastip + " 5004 \| avconv -f s24be -ar 48k -ac 2 -i - -f mp3 -b:a 320K - \| ezstream -c /home/axia/ezstream.xml\r";


    return content;
}

function updsdp() {
    var tnode, tchan, sdp, cfg,tsdp;
    sdp = "";
    cfg = "";
    tnode = document.getElementById("tsrcnode");
    tchan = document.getElementById("tchan");
    tsdp = document.getElementById("txtsdp");
    if(parseInt($(tchan).val()) > 32767) { return; }

    if(tnode && tnode.value && tchan && tchan.value) {
        sdp = mksdp(tnode.value, parseInt(tchan.value));
        cfg = mkcfg(tnode.value, parseInt(tchan.value));
    }
    tsdp.innerHTML = "<p>" + sdp.replace(/\n/gi, "<br>") + "</p>";
    document.getElementById('textblock').value = cfg;
}

</script>
</head>



<body onload="updsdp()">
<p><b>Axia Livewire SDP Generator version 1.1 and Livewire Player</b></p>

<form action="index.php" method='post'>
<table cellpadding="5">
<tr>
    <td>Source node IP address:</td>
    <td><input id="tsrcnode" type="text" value="172.22.0.78" onkeyup="updsdp()" onchange="updsdp()" /></td>
    <td rowspan="5" valign="top" align="left" padding="20">Channel favorites:<p>
    <a href="#" title="9010" onClick="updateValue(this.title, event)">9010</a> ZIP One<br>
    <a href="#" title="9018" onClick="updateValue(this.title, event)">9018</a> Comrex Access<p>
    <a href="#" title="4301" onClick="updateValue(this.title, event)">4301</a> Studio 4 VMode1<br>
    <a href="#" title="10003" onClick="updateValue(this.title, event)">10003</a> Retro FM Tuner Control<p>
    <script>
    function updateValue(val, event) {
    	document.getElementById("tchan").value = val;
    	event.preventDefault();
    }
</script>
</td>
</tr>
<tr>
    <td>Livewire channel number:</td>
    <td><input id="tchan" type="text" value="1" onkeyup="updsdp()" onchange="updsdp()" /></td>
</tr>
<tr>
    <td>Config file for livewire-streamer:</td>
    <td><textarea cols="40" rows="5" readonly="readonly" name= "textblock" id="textblock">test</textarea></td>
</tr>
<tr>
    <td><button id="button1" type="submit">Write config to File</button></td>
    <td><audio controls>
    <source src="http://YOUR-ICECAST-SERVER:8000/axia" type="audio/mpeg">
    <p><a target="_blank" href="http://YOUR-ICECAST-SERVER:8000/axia">PLAY</a>
    </audio>
</td>
</tr>
<tr>
    <td>SDP:</td>
    <td><div id="txtsdp"></div></td>
</tr>
</table>
</form>

<p class="text">
Server-Name: <samp><?php echo $_SERVER['SERVER_NAME']; ?></samp><br />
Document-Root: <samp><?php echo $_SERVER['DOCUMENT_ROOT']; ?></samp><br />
Expect 4...5 seconds delay of Icecast processing.<br /><br />
Enter Livewire channel number and press "Write config" button to save new service configuration and restart
stream service.
</p>


<p></p>
<p><a href="ftp://ftp.zephyr.com/pub/Axia/Tools/sdpgen.htm">SDPgen.htm</a> //
<a href="https://github.com/NeilBetham/livewire-streamer">original Livewire streamer</a></p>

<?php
//if(isset($_POST['field1']) && isset($_POST['field2'])) {
if(isset($_POST['textblock'])) {
    $data = $_POST['textblock'];
    $ret = file_put_contents('/home/axia/stream.sh', $data);
    if($ret === false) {
        die('There was an error writing this file');
    } else {
        echo "$ret bytes written to file";
	exec("/home/axia/stream-restart.sh");
//	exec("/bin/sudo /bin/systemctl stop stream && /bin/sudo /bin/systemctl start stream");
    }
} else {
    die('no post data to process');
}
?>


</body>
</html>
