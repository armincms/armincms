Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'armincms',
      path: '/armincms',
      component: require('./components/Tool'),
    },
  ])
})
