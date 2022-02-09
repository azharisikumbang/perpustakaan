require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const cariInput = document.getElementById('cari-input');

if (cariInput != null) {
	cariInput.addEventListener("keyup", function(event) {
	  if (event.keyCode === 13) {
	    event.preventDefault();
	    document.getElementById("cari-button").click();
	  }
	});
}


