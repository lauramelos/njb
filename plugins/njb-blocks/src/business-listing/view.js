/**
 * Use this file for JavaScript code that you want to run in the front-end 
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any 
 * JavaScript running in the front-end, then you should delete this file and remove 
 * the `viewScript` property from `block.json`. 
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */
// Puedes poner esto en un archivo JS o en un bloque <script> en el footer
jQuery(document).ready(function($) {
    // Aplica select2 a todos los selects con la clase .select2
    $('select.select2').select2();
});

document.addEventListener("submit", (e) => {
  if (e.target.id === "cpt-filters") {
    e.preventDefault();
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    
    fetch(`/wp-json/njb/v1/listing?${params}`)
      .then(res => res.json())
      .then(posts => {
        const container = document.querySelector("#cpt-listing-results");
        container.innerHTML = posts.length
        ? posts.map( item => `
          <div class="business-listing-item">
              ${item.logo ? `<div class="business-logo">${item.logo}</div>` : ""}
              <h5 class="business-website">
                  ${item.website ? `<a href="${item.website}" target="_blank" rel="noopener">${item.title}</a>` : item.title}
              </h5>
              ${item.sectors && item.sectors.length ? `
                  <p class="business-sector">
                      ${item.sectors.map(sector => `${sector['name']}`).join(' ')}
                  </p>
              ` : ""}
              ${item.address && item.address.length ? `
                  <p class="business-location">
                      <span class="dashicons dashicons-location"></span>
                      ${item.address.join(', ')}
                  </p>
              ` : ""}
              ${item.phone_number ? `
                  <p class="business-phone">
                      <span class="dashicons dashicons-phone"></span>
                      <a href="tel:${item.phone_number.replace(/\s+/g, '')}">${item.phone_number}</a>
                  </p>
              ` : ""}
              ${item.email ? `
                  <p class="business-email">
                      <span class="dashicons dashicons-email"></span>
                      <a href="mailto:${item.email}">${item.email}</a>
                  </p>
              ` : ""}
          </div>
        `).join("")
        : "<p>No results found.</p>";
      });
    }
});

