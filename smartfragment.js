var hashChangeTest = false;
var hashChangeTestTimeout;
var lastHash = location.hash;
var ignoreNextHashChange = false;
var hashChangeSupported;
var hashChangeCallback;
var smartFragmentDebug;

function setHashSilently(hash) {
  if (hash == location.hash || "#" + hash == location.hash) return;
  if (hashChangeSupported) {
    ignoreNextHashChange = true;
  }
  location.hash = hash;
  lastHash = hash;
}

function onHashChange() {
  if (hashChangeTest) {
    clearTimeout(hashChangeTestTimeout);
    hashChangeTest = false;
    ignoreNextHashChange = true;
    location.hash = lastHash;
    hashChangeSupported = true;
    if (smartFragmentDebug) {
      console.debug("supported");
    }
    return;
  }
  if (ignoreNextHashChange) {
    ignoreNextHashChange = false;
    if (smartFragmentDebug) console.debug("ignored");
    return;
  }
  if (smartFragmentDebug) {
    console.debug("hash change event");
  }
  updateHash(location.hash);
}

function onHashClick() {
  if (smartFragmentDebug) {
    console.debug("click");
  }
  updateHash(this.hash);
}

function setupSmartFragment(callback, debug) {
  hashChangeCallback = callback;
  smartFragmentDebug = debug;
  document.body.onhashchange = onHashChange;
  var oldHash = location.hash
  hashChangeTest = true;
  if (location.hash == "") {
    location.hash = "#t";
  } else {
    location.hash = "#";
  }
  hashChangeTestTimeout = setTimeout(function(){
        hashChangeSupported = false;
        if (smartFragmentDebug) {
          console.debug("not supported");
        }
        location.hash = oldHash;
        setupOnClicks();
    }, 1);
}

function setupOnClicks() {
  var links = document.getElementsByTagName("a");
  for (var i = 0; i < links.length; ++i) {
    var href = links[i].attributes.getNamedItem("href")
    if (href && href.value.trim().substring(0,1) == "#") {
      links[i].onclick = onHashClick;
    }
  }
}

function updateHash(hash) {
  if (smartFragmentDebug) {
    console.debug("update hash: " + hash);
  }
  if (hashChangeCallback(hash)) {
    lastHash = location.hash;
  } else {
    if (smartFragmentDebug) console.debug("reverting to: " + lastHash);
    setHashSilently(lastHash);
  }
}