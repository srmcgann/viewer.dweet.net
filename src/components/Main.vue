<template>
  <div class="main">
    pshare<br><br>
    a distributed, browser based operating system<br><br>
    registration is free and anonymous<br><br><br>
    upload, manage, use, and share your data...<br><br>
    <button @click="state.register()" class="button">register</button>  
  </div>
</template>

<script>

export default {
  name: 'Main',
  props: ['state'],
  data(){
    return {
      testData : "it works!"
    }
  },
  methods:{
    login(){
      this.state.login()
    },
    logout(){
      this.state.logout()
    },
    register(){
      this.state.register()
    },
    settings(){
      this.state.closeModals()
      this.state.showModal('settings')
    }
  }
}

</script>

<style scoped>
  .main{
    padding: 20px;
    color: #fff;
    border: 1px solid #4f84;
  }
</style>
