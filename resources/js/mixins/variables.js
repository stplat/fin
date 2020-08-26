export default {
  data() {
    return {
      assetBase: '',
      assetImages: '',
    }
  },
  mounted() {
    this.assetBase = document.querySelector('base').href;
    this.assetImages = document.querySelector('base').href + 'images/photos/';
  }
}
