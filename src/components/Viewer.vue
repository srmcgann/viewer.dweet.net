<template>
  <button
    class="cancelButton button"
    id="viewerClose"
    @keyup.esc="state.closeModals()"
    ref="viewerClose"
    @click="state.closeModals()"
  >close</button>
  <div class="viewer">
   <iframe :src="src" class="viewerFrame"></iframe>
  </div>
</template>

<script>

export default {
  name: 'Viewer',
  props: ['state', 'src'],
  data(){
    return {
    }
  },
  methods:{
    checkKeys(e){
      console.log(e)
    }
  },
  mounted(){
    this.$refs.viewerClose.focus()
  }
}

</script>

<style scoped>
  .viewerFrame{
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    border: none;
    height: 100vh;
  }
  #viewerClose{
    position: absolute;
    right: 0;
    margin-right: 50px;
    z-index: 210;
    margin-top: 50px;
  }
  .viewer{
    text-align: center;
    position: fixed;
    top: 0;
    padding: 20px;
    left: 0;
    color: #fff;
    width: 100vw;
    height: 100vh;
    z-index: 200;
    background: #011e;
  }
</style>
