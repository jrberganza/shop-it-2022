module.exports = {
  transpileDependencies: [
    'vuetify',
  ],
  devServer: {
    proxy: {
      '^/api': {
        target: 'http://localhost:14646',
        changeOrigin: true
      },
    }
  }
};
