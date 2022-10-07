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
  props: ["location"],
  data() {
    return {
      accessToken: MAPBOX_ACCESS_TOKEN,
      map: null,
      marker: null,
    };
  },
  mounted() {
    mapboxgl.accessToken = this.accessToken;

    this.map = new mapboxgl.Map({
      container: this.$refs.mapContainer,
      style: "mapbox://styles/mapbox/streets-v11",
      center: this.location,
      zoom: 8,
    });

    this.marker = new mapboxgl.Marker()
      .setLngLat(this.location)
      .addTo(this.map);
  },
};
</script>
