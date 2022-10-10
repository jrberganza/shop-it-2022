<template>
  <div ref="mapContainer" class="basemap"></div>
</template>

<style>
.basemap {
  height: 300px;
}
</style>

<script>
import mapboxgl from "mapbox-gl";
import { MAPBOX_ACCESS_TOKEN } from "../../environment.js";

export default {
  name: "Map",
  model: {
    prop: 'location',
    event: 'input'
  },
  props: {
    location: {
      type: Array,
      default: () => null,
    },
    input: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      accessToken: MAPBOX_ACCESS_TOKEN,
      /** @type {any} */ map: null,
      /** @type {any} */ marker: null,
      /** @type {[number, number] | null} */ markerLocation: null,
    };
  },
  watch: {
    location(newVal) {
      this.markerLocation = newVal;

      this.setMarker(newVal);

      if (newVal) {
        this.map.flyTo({ center: newVal, zoom: 14 });
      } else {
        this.map.flyTo({ center: [-90.5314, 14.6228], zoom: 14 });
      }
    },
    markerLocation(newVal) {
      this.$emit('input', newVal);

      this.setMarker(newVal);
    }
  },
  methods: {
    setMarker(lngLat) {
      if (lngLat) {
        this.marker.setLngLat(lngLat).addTo(this.map);
      } else {
        this.marker.remove();
      }
    }
  },
  mounted() {
    mapboxgl.accessToken = this.accessToken;

    this.map = new mapboxgl.Map({
      container: this.$refs.mapContainer,
      style: "mapbox://styles/mapbox/streets-v11",
      center: this.location || [-90.5314, 14.6228],
      zoom: 14,
    });

    this.marker = new mapboxgl.Marker();

    if (this.location) {
      this.markerLocation = this.location;
    }

    if (this.input) {
      this.map.on("click", (e) => {
        this.markerLocation = [e.lngLat.lng, e.lngLat.lat]
      })
    }
  },
};
</script>
