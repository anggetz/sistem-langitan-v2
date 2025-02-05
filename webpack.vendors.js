module.exports = {
  output: 'public/assets',
  entry: {
    keenicons: [
      {
        src: ['resources/metronic/vendors/keenicons/**/style.css'],
        dist: '/vendors/keenicons/styles.bundle.css',
        bundle: true,
      },
      {
        src: [
          'resources/metronic/vendors/keenicons/duotone/fonts',
          'resources/metronic/vendors/keenicons/filled/fonts',
          'resources/metronic/vendors/keenicons/outline/fonts',
          'resources/metronic/vendors/keenicons/solid/fonts',
        ],
        dist: '/vendors/keenicons/fonts',
      }
    ],
    '@form-validation': [
      {
        src: ['resources/metronic/vendors/@form-validation/umd/styles'],
        dist: '/vendors/@form-validation',
      },
      {
        src: [
          'resources/metronic/vendors/@form-validation/umd/bundle/popular.min.js',
          'resources/metronic/vendors/@form-validation/umd/bundle/full.min.js',
          'resources/metronic/vendors/@form-validation/umd/plugin-bootstrap5/index.min.js',
        ],
        dist: '/vendors/@form-validation/form-validation.bundle.js',
        bundle: true,
      },
    ],
    leaflet: [
      {
        src: ['node_modules/leaflet/dist/leaflet.css'],
        dist: '/vendors/leaflet/leaflet.bundle.css',
        bundle: true,
      },
      {
        src: [
          'node_modules/leaflet/dist/leaflet.js',
          'node_modules/esri-leaflet/dist/esri-leaflet.js',
          'node_modules/esri-leaflet-geocoder/dist/esri-leaflet-geocoder.js',
        ],
        dist: '/vendors/leaflet/leaflet.bundle.js',
        bundle: true,
      },
    ],
    apexcharts: [
      {
        src: ['node_modules/apexcharts/dist/apexcharts.css'],
        dist: '/vendors/apexcharts/apexcharts.css',
      },
      {
        src: ['node_modules/apexcharts/dist/apexcharts.min.js'],
        dist: '/vendors/apexcharts/apexcharts.min.js',
      },
    ],
    prismjs: [
      {
        src: [
          'node_modules/prismjs/prism.js',
          'node_modules/prismjs/components/prism-markup.js',
          'node_modules/prismjs/components/prism-markup-templating.js',
          'node_modules/prismjs/components/prism-bash.js',
          'node_modules/prismjs/components/prism-javascript.js',
          'node_modules/prismjs/components/prism-css.js',
          'node_modules/prismjs/plugins/normalize-whitespace/prism-normalize-whitespace.js',
          'resources/metronic/vendors/prismjs/prismjs.init.js'
        ],
        dist: '/vendors/prismjs/prismjs.min.js',
        bundle: true,
      }
    ],
    clipboard: [
      {
        src: ['node_modules/clipboard/dist/clipboard.min.js'],
        dist: '/vendors/clipboard/clipboard.min.js'
      }
    ]
  }
};
