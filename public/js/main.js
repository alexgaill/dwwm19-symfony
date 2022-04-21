const li = document.getElementsByClassName('clickLi');
console.log(li);

for (const elmt of li) {
    elmt.addEventListener('click', () => {
        alert("C'est cliqué");
    })
}