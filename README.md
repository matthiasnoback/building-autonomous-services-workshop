# Set up DNS

Run the following commands to register this project's [`dnsmasq`](http://www.thekelleys.org.uk/dnsmasq/doc.html) service as a nameserver for `.localhost` domain names:

## Mac OS X

```bash
sudo mkdir -p /etc/resolver
echo "nameserver 127.0.0.1
port 53535" | sudo tee /etc/resolver/localhost
```

## Linux

Add this line to `/etc/resolv.conf`: 

```
server=/localhost/127.0.0.1#53535
```

## Windows

- Find your network adapter in `Network Connections`.
- Right click on it and go to `Properties`.
- Select TCP/IPv4 and click on `Properties`.
- Under `General` tab, click on `Advanced`. This will open `Advanced TCP/IP Settings` window.
- Check on `Append these DNS suffixes (in order)` and click on `Add...`
- Add `localhost` as a domain suffix and click `OK` in all previous windows you just opened in a chain.

# If all else fails

Instead of setting up the nameserver, you can always manually configure the following host names to resolve to `127.0.0.1` (or the IP of the VM that's running Docker, i.e. in case you're using Boot2docker): 

```
# in /etc/hosts (or the equivalent on Windows)
127.0.0.1 dashboard.localhost catalog.localhost sales.localhost purchase.localhost
```
