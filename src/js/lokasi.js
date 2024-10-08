/*!
 *  v1.0.2 (https://velocitydeveloper.com)
 * Copyright 2013-2024 Velocity Developer
 * Licensed under GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
 */
(function ($) {
  // Inisialisasi chained untuk elemen select saat dokumen siap
  $(document).ready(function () {
    // jika elemen #cp_city ada, jalankan fungsi chained
    if ($("#cp_city").length) {
      $("#cp_city").chained("#cp_province");
    }
    if ($("#cp_district").length) {
      $("#cp_district").chained("#cp_city");
    }
  });
})(jQuery);
document.addEventListener("DOMContentLoaded", function () {
  // Fungsi untuk memeriksa dan fetch data jika belum ada di local storage
  function fetchDataIfNotExists(storageKey, url) {
    if (!Lockr.get(storageKey)) {
      fetch(url)
        .then((response) => {
          if (!response.ok) {
            throw new Error(
              "Network response was not ok " + response.statusText
            );
          }
          return response.json();
        })
        .then((data) => {
          Lockr.set(storageKey, data);
          // console.log(
          //   `Data berhasil disimpan ke local storage dengan kunci: ${storageKey}`
          // );
        })
        .catch((error) =>
          console.error(`Error fetching ${storageKey} data:`, error)
        );
    } else {
      // console.log(
      //   `Data dengan kunci ${storageKey} sudah ada di local storage.`
      // );
    }
  }

  // Memeriksa dan fetch data jika belum ada di local storage
  fetchDataIfNotExists("stateData", customPluginData.jsonState);
  fetchDataIfNotExists("cityData", customPluginData.jsonCity);
  fetchDataIfNotExists("districtData", customPluginData.jsonDistrict);

  function removeEmptyOption(selectElement) {
    const options = selectElement.options;

    for (let i = options.length - 1; i >= 0; i--) {
      if (options[i].value === "") {
        selectElement.remove(i);
      }
    }
  }
  // Fungsi untuk mengisi elemen <select> dengan data dari local storage
  function populateSelect(elementId, localStorageKey) {
    const selectElement = document.getElementById(elementId);
    if (selectElement) {
      removeEmptyOption(selectElement);
      const currentValue = selectElement.getAttribute("data-current");
      const data = Lockr.get(localStorageKey);
      if (data) {
        data.forEach((item) => {
          const option = document.createElement("option");
          option.value = item.value || item.label;
          if (item.value === currentValue) {
            option.setAttribute("selected", "selected");
          }
          if (item.city_id) {
            option.setAttribute("data-child", item.city_id);
            option.textContent = item.value;
          } else if (item.state) {
            option.setAttribute("data-child", item.state);
            option.setAttribute("data-parent", item.id);
            option.textContent = item.value;
          } else {
            option.setAttribute("data-parent", item.value);
            option.textContent = item.label;
          }
          selectElement.appendChild(option);
        });
        // Trigger the chained function to update
        jQuery(selectElement).trigger("change");
      } else {
        // console.log(
        // `Tidak ada data ${localStorageKey} di local storage atau data tidak valid.`
        // );
      }
    } else {
      // console.log(`Elemen <select> dengan ID '${elementId}' tidak ditemukan.`);
    }
  }

  // Panggil fungsi untuk mengisi elemen <select> dengan data provinsi dan kota
  populateSelect("cp_province", "stateData");
  populateSelect("cp_city", "cityData");
  populateSelect("cp_district", "districtData");
});
