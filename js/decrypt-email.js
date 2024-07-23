function decrypt(element) {
    var coded = element.getAttribute('data-coded');
    var key = element.getAttribute('data-key');
    var shift = coded.length;
    var link = "";
    for (i=0; i<shift; i++) {
        if (key.indexOf(coded.charAt(i))==-1) {
            ltr = coded.charAt(i);
            link += (ltr);
        } else { 
            ltr = (key.indexOf(coded.charAt(i))-shift+key.length) % key.length;
            link += (key.charAt(ltr));
        };
    };
    element.innerHTML = ("<a href='mailto:"+link+"' >"+link+"</a>");
};
document.querySelectorAll('.encrypted-email').forEach(decrypt);
