const getDistinctCityNames = () => {
    const url = 'http://localhost/blog/index.php/wp-json/meteorites/v1/city_names/';

    return new Promise((resolve, reject) => {
        fetch(url).then(response => {
            return response.json();
        }).then(data => {
            resolve(JSON.parse(data));
        });
    });
}

getDistinctCityNames().then(data => {
    autocomplete(document.getElementById('city_name'), data.map(object => object.meteorite_city_name));
});

const autocomplete = (input, list) => {
    let currentFocus;

    input.addEventListener("input", (e) => {
        let val = e.target.value;

        closeAllLists();

        if(!val) { return false; }

        currentFocus = -1;

        let listDIV = document.createElement("DIV");
        listDIV.setAttribute("id", e.target.id + "autocomplete-list");
        listDIV.setAttribute("class", "autocomplete-items");

        e.target.parentNode.appendChild(listDIV);

        let option;

        for(let i = 0; i < list.length; i++) {
            if(list[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                option = document.createElement("DIV");

                option.innerHTML = "<strong>" + list[i].substr(0, val.length) + "</strong>" + list[i].substr(val.length) + "<input type='hidden' value='" + list[i] + "'>";

                option.addEventListener("click", (e) => {
                    input.value = e.target.getElementsByTagName("input")[0].value;
                    closeAllLists();
                });

                listDIV.appendChild(option);
            }
        }
    });

    input.addEventListener("keydown", (e) => {
        let listDIV = document.getElementById(e.target.id + "autocomplete-list");

        if(listDIV) {
            listDIV = listDIV.getElementsByTagName("div");
        }
        if(e.keyCode == 40) {
            currentFocus++;
            addActive(listDIV);
        } else if(e.keyCode == 38) {
            currentFocus--;
            addActive(listDIV);
        } else if(e.keyCode == 13) {
            e.preventDefault();

            if(currentFocus > -1) {
                if(listDIV) {
                    listDIV[currentFocus].click();
                }
            }
        }
    });

    const addActive = (options) => {
        if(!options) { return false; }

        removeActive(options);

        if(currentFocus >= options.length) {
            currentFocus = 0;
        }
        if(currentFocus < 0) {
            currentFocus = (options.length - 1);
        }

        options[currentFocus].classList.add("autocomplete-active");
    }

    const removeActive = (options) => {
        for(let i = 0; i < options.length; i++) {
            options[i].classList.remove("autocomplete-active");
        }
    }

    const closeAllLists = (elmnt) => {
        let x = document.getElementsByClassName("autocomplete-items");

        for(let i = 0; i < x.length; i++) {
            if(elmnt != x[i] && elmnt != input) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    document.addEventListener("click", (e) => {
        closeAllLists(e.target);
    });
}

const getPoints = () => {
    const LatLimit = Math.atan(Math.sinh(Math.PI)) * 180 / Math.PI;

    let city = document.getElementById('city_name').value !== '' ? document.getElementById('city_name').value : 'null';
    let from = document.getElementById('from_year').value !== '' ? document.getElementById('from_year').value : 'null';
    let to = document.getElementById('to_year').value !== '' ? document.getElementById('to_year').value : 'null';
    let latOne = document.getElementById('lat_one').value !== '' ? parseFloat(document.getElementById('lat_one').value) % LatLimit : 'null';
    let lngOne = document.getElementById('lng_one').value !== '' ? document.getElementById('lng_one').value : 'null';
    let latTwo = document.getElementById('lat_two').value !== '' ? parseFloat(document.getElementById('lat_two').value) % LatLimit : 'null';
    let lngTwo = document.getElementById('lng_two').value !== '' ? document.getElementById('lng_two').value : 'null';

    const url = "http://localhost/blog/index.php/wp-json/meteorites/v1/filter/" + city + '&' + from + '&' + to + '&' + latOne + '&' + lngOne + '&' + latTwo + '&' + lngTwo;

    return new Promise((resolve, reject) => {
        fetch(url).then(response => {
            return response.json();
        }).then(data => {
            resolve(JSON.parse(data));
        }).catch(err => {
            reject(err);
        });
    });
}