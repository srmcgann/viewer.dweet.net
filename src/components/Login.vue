<template>
  <div class="login">
    <div class="inputForm">
      <div class="sectionTitle">LOGIN</div><br>
      <input
        type="text"
        v-model="loginUserName"
        ref="loginModal"
        v-on:keyup.enter="login()"
        class="basicTextInput loginInput"
        placeholder="user name / email"
      >
      <input
        type="password"
        v-model="loginPassword"
        v-on:keyup.enter="login()"
        class="basicTextInput loginInput"
        placeholder="password"
      ><br>
      <div id="loginError" class="status">error</div>
      <button @click="login()" class="button">login</button>
    </div>
    <button
      class="cancelButton button"
      id="loginCancel"
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
      loginUserName: '',
      loginPassword: ''
    }
  },
  methods:{
    login(){
      return
      if(!this.loginPassword || !this.loginUserName) return
      let sendData = {
        user: this.loginUserName,
        password: this.loginPassword,
        location: decodeURIComponent(window.location.pathname)
      }
      fetch(this.state.baseURL + '/login.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        if(data[0]){
          this.state.loggedinUser = data[1]
          this.state.token = data[1].passhash
          this.state.loggedinUserHash = data[1].passhash
          this.state.loggedinUserID = data[1].id
          this.state.loggedinUserAvatar = data[1].avatar
          this.state.loggedinUserBasicIcons = data[1].basicIcons
          this.state.loggedinUserSnapToGrid = data[1].snapToGrid
          this.state.loggedinUserLocation = data[1].currentLocation
          this.state.loggedinUserName = data[1].name
          this.state.loggedinUserEmail = data[1].email
          this.state.admin = !!(+data[1].admin)
          this.state.setCookie()
          window.location.reload()
          //this.state.loadLoggedInUserData()
          //this.state.closeModals()
          //this.state.loggedin = true
        }else{
          let el = document.querySelector('#loginError')
          el.style.display = 'block'
          el.innerHTML = data[2]
        }
      })
    }
  },
  mounted(){
    this.$refs.loginModal.focus()
  }
}

</script>


<style scoped>
  .status{
    background: #400;
    width: 400px;
    display: none;
    margin-left: auto;
    margin-right: auto;
    color: #f44;
  }
  .sectionTitle{
    margin: 20px;
    display: inline-block;
    font-size: 32px;
    font-weight: 900;
  }
  .loginInput{
    margin: 3px;
  }
  #loginCancel{
    position: absolute;
    right: 0;
    margin-right: 50px;
    margin-top: 50px;
  }
  .login{
    text-align: center;
    position: fixed;
    top: 0;
    padding: 20px;
    left: 0;
    color: #fff;
    width: 100vw;
    height: 100vh;
    z-index: 10;
    background: #011e;
  }
</style>
