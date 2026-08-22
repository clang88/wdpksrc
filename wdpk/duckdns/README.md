# DuckDNS

Dynamic DNS via [DuckDNS](https://www.duckdns.org). A background script announces your
current public IP to DuckDNS every 5 minutes, so your domain always resolves to your
My Cloud's IP.

## Configuration

The DuckDNS update URL (domain + token) is configured in the web UI:
**Apps → DuckDNS**.

The URL has the following format:

```
https://www.duckdns.org/update?domains={domain}&token={token}&ip=
```

- `{domain}` — your DuckDNS domain name (e.g. `lang-home-bz`)
- `{token}` — the token shown in your DuckDNS account
- `ip=` — left empty; DuckDNS then uses the IP of the connecting host

The configuration is stored in `config/duckdns.conf` inside the package directory
(persistent across reboots). The token is masked in the web UI and the config file
is only readable by root.

The background script re-reads the configuration on every update cycle, so changes
made in the web UI take effect within 5 minutes without a restart.
