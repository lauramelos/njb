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
        ? posts.map(p => `
            <div class="business-listing-item">
            ${p.logo ? `<div class="business-logo">${p.logo}</div>` : ""}
            <h5>${p.title}</h5>
            ${p.sectors && p.sectors.length ? `
                <p class="business-sector">
                ${p.sectors.map(sector => `${sector['name']}`).join(' ')}
                </p>
            ` : ""}
            ${(p.country && p.country.length) ? `
                <p class="business-location">
                ${(p.state && p.state.length) ? p.state.map(s => `${s['name']}`).join(', ') : ''}
                ${(p.state && p.state.length && p.country && p.country.length) ? ', ' : ''}
                ${p.country.map(c => `${c['name']}`).join(', ')}
                </p>
            ` : ""}
            </div>
        `).join("")
        : "<p>No results found.</p>";
      });
    }
});

