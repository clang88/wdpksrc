---
name: wd-mycloud-ui
description: 'Build or modify web UIs for WD My Cloud apkg packages (wdpksrc repo). Use when: creating an app config page, working with wd_ajax, page_load, center_type, apkg.rc/apkg.xml, login_checker.php, the _T() language system, or debugging why an app page renders in the wrong place in the My Cloud web UI.'
---

# WD My Cloud App UI (wdpksrc)

Knowledge for building web UIs for WD My Cloud apkg packages in this repo, distilled from the official `downloaded_samples/` apps (InternalBackups, USBBackups, GoodSync) and the `wdpk/persistent-ssh` implementation.

## Package Anatomy

```
wdpk/<app-name>/
├── apkg.rc          # SOURCE OF TRUTH — human-maintained package metadata
├── apkg.xml         # GENERATED from apkg.rc by mksapkg — gitignored, do not hand-edit
├── apkg.sign        # Signature placeholder
├── build.sh         # Runs ../../mksapkg -E -s -m <model> per model
├── build-w-docker.sh
├── install.sh       # Copies package to $NASPROG (e.g. /shares/Volume_1/Nas_Prog/<app>)
├── init.sh          # Creates symlinks at boot (web files, binaries, config)
├── start.sh / stop.sh / remove.sh / clean.sh / preinst.sh
└── web/             # Web UI, served from /var/www/apps/<app>/
    ├── index.php    # The config page (see "Page Structure" below)
    ├── <api>.php    # JSON backend(s)
    ├── language.js  # _T() language loader
    └── lang/*.xml   # 18 language files (en-US + 17 others)
```

### apkg.rc key fields

| Field | Meaning |
|-------|---------|
| `CenterType: 1` | **Render inline** in the main web UI (Apps sidebar → content area). Use this for config pages. |
| `CenterType: 0` | Open as a **separate page/tab** (standalone). |
| `IndividualFlag: 1` | Individual app entry in the Apps list. |
| `AddonIndexPage: index.php` | Entry page, served at `/apps/<app>/index.php`. |
| `AddonUsedPort:` | Port for apps that run their own web server (leave empty if using the built-in PHP). |

`apkg.xml` is regenerated from `apkg.rc` by `mksapkg` during `build.sh` and is gitignored (`wdpk/*/*.xml`). If you change metadata, edit **apkg.rc only**.

### init.sh web deployment convention

```sh
WEBPATH="/var/www/apps/${APKG_MODULE}"
mkdir -p $WEBPATH
ln -sf $path/web/* $WEBPATH
```

All web assets must be reachable under `/apps/<app>/...` — use that path in every `src=`, `url:`, and API URL. (InternalBackups does exactly this; a package linking to `/var/www/<app>` instead will break.)

## Page Structure (the critical part)

The WD web UI shell loads the app's `index.php` and **moves the static `<body>` content into the content area** (right of the Apps sidebar). Then it calls a global `page_load()` function.

Rules:

1. **Body must contain static HTML.** The shell relocates body markup. If the body is empty and JS builds the UI at runtime via `$("body").append(...)`, the content lands in the *main document's* body — i.e. **below the whole layout** (classic bug: page renders under the menu instead of in the content pane).
2. **Define `function page_load() { ... }`** — the shell calls it after injection. Do all init there (language load, data fetch, widget init). Guard with a `var PS_LOADED = false` flag so it runs once.
3. **Add a fallback** in the body so the page also works when opened directly in a browser tab:
   ```js
   $(document).ready(function () {
       if (typeof page_load == "function") page_load();
   });
   ```
4. **Scripts go in `<head>`** (like InternalBackups): `<script src="/apps/<app>/language.js">` plus an inline `<script>` with your functions.
5. **Use WD native CSS classes** so the page matches the shell:
   - `h1_content header_2` — section heading (wrap text in `<span class="_text" ...>`)
   - `field_top` — form row / button row
   - `hr_0_content > .hr_1` — horizontal separator
   - `wd_select` / `ul_obj` — WD dropdown widgets (see `schedule_select.js` in InternalBackups for the full pattern)
   - `WDLabelDiag` / `WDLabelBodyDialogue` — dialogs (use with the `.overlay()` plugin)
   - `jAlert(msg, "warning")` — native alert dialog
   - `inputReset()`, `init_tooltip()`, `init_switch()` — common init helpers
   Only add a small inline `<style>` block for things the shell doesn't style (e.g. a monospace textarea).

Minimal working page skeleton:

```html
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="PRAGMA" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-Control" content="no-cache">
<script type="text/javascript" src="/apps/<app>/language.js"></script>
<script type="text/javascript">
var API_URL = "/apps/<app>/api.php";
var LOADED = false;
function page_load() {
    if (LOADED) return;
    LOADED = true;
    ready_language();
    /* fetch data, init widgets ... */
}
</script>
</head>
<body>
<div class="h1_content header_2"><span class="_text" lang="_app" datafld="title"></span></div>
<div class="field_top"><span class="_text" lang="_app" datafld="desc"></span></div>
<div class="hr_0_content"><div class="hr_1"></div></div>
<!-- form fields, buttons ... -->
<script type="text/javascript">
$(document).ready(function () {
    if (typeof page_load == "function") page_load();
});
</script>
</body>
</html>
```

## AJAX: `wd_ajax()`

The shell provides a global `wd_ajax(options)` — a jQuery-`$.ajax`-style wrapper that carries the WD session/auth context. **Always use it instead of `fetch()`** for app↔backend calls:

```js
wd_ajax({
    type: "GET",            // or "POST"
    url: API_URL,
    dataType: "json",       // or "xml"
    data: { action: "read" },
    error: function () { /* ... */ },
    success: function (r) { /* ... */ }
});
```

- POST data is sent as **form-encoded fields** (`data: { action: "write", keys: ... }`), not JSON bodies.
- For XML (language files, folder trees): `dataType: "xml"`, `async: false`, `cache: false`.

## Backend PHP Pattern

Mirror `downloaded_samples/InternalBackups/web/internal_backup.php`:

```php
<?php
session_start();
$r = new stdClass();
$r->success = false;

include ($_SERVER['DOCUMENT_ROOT']."/web/lib/login_checker.php");
if (login_check() != 1) {
    $r->message = "Authentication required";
    echo json_encode($r);
    exit;
}

$action = $_POST['action'];
if ($action == "") $action = $_GET['action'];

switch ($action) {
    case "read":  /* ... */ echo json_encode($r); break;
    case "write": /* ... */ echo json_encode($r); break;
    default:      $r->message = "Invalid action"; echo json_encode($r); break;
}
```

Notes:
- `login_checker.php` lives at `/var/www/web/lib/login_checker.php` (note the `web/lib` path).
- **On production firmware `login_check()` is a stub that always returns 1** — it does NOT enforce authentication. Do not rely on it for security; it only matters for the standalone-page flow. The real protection for `CenterType: 1` apps is that the page only renders inside the logged-in shell.
- Return `{"success": bool, ...}` JSON; the JS `success` callback checks `r.success`.

## Language System (`_T()`)

- `web/language.js` loads `web/lang/<locale>.xml` via `wd_ajax` (async:false) and exposes `_T(category, id)` which looks up `<text><category><id>` with en-US fallback.
- Locale index comes from the global `MULTI_LANGUAGE` (0=en-US, 1=fr-FR, 2=it-IT, 3=de-DE, 4=es-ES, 5=zh-CN, 6=zh-TW, 7=ko-KR, 8=ja-JP, 9=ru-RU, 10=pt-BR, 11=cs-CZ, 12=nl-NL, 13=hu-HU, 14=no-NO, 15=pl-PL, 16=sv-SE, 17=tr-TR).
- Prefix your own functions per app to avoid collisions (`_T_PS`, `ps_ready_language`, `PS_XML_LANGUAGE`) — the global `_T` is shared by all WD apps.
- In HTML, mark translatable text: `<span class="_text" lang="_category" datafld="id"></span>`; the `language()` function fills them.
- Language XML shape:
  ```xml
  <?xml version="1.0" encoding="utf-8" ?>
  <text>
      <_common>
          <error len="999">Error</error>
      </_common>
      <_myapp>
          <title len="999">My App</title>
      </_myapp>
  </text>
  ```
- Create all 18 files; non-English ones may start as copies of en-US.

## Build & Test Loop

```sh
cd wdpk/<app>
./build-w-docker.sh        # or ./build.sh (needs mksapkg for each model)
# produces packages/<app>/<model>_<app>_<ver>.bin
```

Install the `.bin` on the device, then check the Apps page in the web UI. For quick iteration, the web files are symlinked from the package dir (`/shares/Volume_1/Nas_Prog/<app>/web`), so you can edit files on the device and refresh — no reinstall needed for web/ changes.

## Debugging Checklist

| Symptom | Cause / Fix |
|---------|-------------|
| Page renders **below** the whole layout | Body was empty / JS-built. Make body static HTML; init in `page_load()`. |
| Content in right place but **blank** | Shell didn't call `page_load()` (or scripts not loaded). Ensure the body fallback `$(document).ready` calls it. |
| 404 on `language.js` / API | `init.sh` links to wrong path. Must be `/var/www/apps/<app>`. |
| UI strings empty | `MULTI_LANGUAGE` index out of range of `lang_array`, or wrong `lang`/`datafld` in markup. |
| API returns auth error | `login_checker.php` include path wrong, or page opened outside the shell. |
| App missing from Apps sidebar | `CenterType`/`IndividualFlag` in **apkg.rc** (not apkg.xml); rebuild. |

## Reference Files in This Repo

- `downloaded_samples/InternalBackups/web/index.php` — full-featured example (tables, dialogs, folder picker, `page_load`)
- `downloaded_samples/InternalBackups/web/language.js` — language loader template
- `downloaded_samples/InternalBackups/web/schedule_select.js` — `wd_select` dropdown widget pattern
- `downloaded_samples/InternalBackups/web/folder_tree.php` — folder-picker backend (`cgi_read_open_tree`)
- `downloaded_samples/_built-in_libs/login_checker.php` — the login stub
- `wdpk/persistent-ssh/` — minimal working example of everything above (one textarea + two buttons)
