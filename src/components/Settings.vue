<template>
  <div class="settings">
    settings component
    <button
      class="cancelButton button"
      @click="state.closeModals()"
    >close</button>
  </div>
</template>

<script>

export default {
  name: 'Login',
  props: ['state'],
  data(){
    return {
      testData : "it works!"
    }
  }
}

</script>

<style scoped>
  .settings{
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px;
    width: 100vw;
    height: 100vh;
    z-index: 10;
    background: #123c;
  }
</style>
