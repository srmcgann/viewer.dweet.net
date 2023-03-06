<template>
  <div class="register">
    <div class="inputForm">
      <div class="sectionTitle">REGISTER</div><br>
      <input
        type="text"
        v-model="registerUserName"
        autofocus
        ref="registerModal"
        @input="checkavailability()"
        v-on:keyup.enter="register()"
        class="basicTextInput registerInput"
        placeholder="user name / email"
      >
      <div v-if="!this.available" id="unavailableError" class="unavailable">user name unavailable!</div>
      <input
        type="password"
        v-model="registerPassword"
        v-on:keyup.enter="register()"
        class="basicTextInput registerInput"
        placeholder="password"
      ><br>
      <input
        type="password"
        v-model="confirmPassword"
        @input="validatePW()"
        v-on:keyup.enter="register()"
        class="basicTextInput registerInput"
        placeholder="confirm password"
      ><br>
      <div
        ref="pwsMatch"
        class="status"
        :class="{'good':pwsMatch, 'noGood':!pwsMatch}"
        v-html="pwsMatch?'passwords match!':'passwords don\'t match!'"
      ></div>
      <input
        type="text"
        v-model="registerEmail"
        @input="validatePW()"
        v-on:keyup.enter="register()"
        class="basicTextInput registerInput"
        placeholder="email (optional, for recovery)"
      ><br>
      <div id="registerError" class="status">error</div>
      <button @click="register()" class="button">submit</button>
    </div>
    <button
      class="cancelButton button"
      id="registerCancel"
      @click="state.closeModals()"
    >close</button>
  </div>
</template>

<script>

export default {
  name: 'Register',
  props: ['state'],
  data(){
    return {
      registerUserName: '',
      registerPassword: '',
      confirmPassword: '',
      registerEmail: '',
      pwsMatch: false,
      available: true
    }
  },
  methods:{
    validatePW(){
      this.pwsMatch = false
      if(this.registerPassword && this.confirmPassword){
        this.$refs.pwsMatch.style.display='block'
        this.pwsMatch = (this.registerPassword == this.confirmPassword)
      } else {
        this.$ref.pwsMatch.style.display='none'
      }
    },
    checkavailability(){
      if(!this.registerUserName) return
      let sendData = {
        userName: this.registerUserName
      }
      fetch(this.state.baseURL + '/checkavailability.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        this.available = data[0]
      })
    },
    register(){
      if(!this.pwsMatch || !this.registerPassword || !this.registerUserName || !this.confirmPassword || !this.available) return
      let sendData = {
        user: this.registerUserName,
        password: this.registerPassword,
        email: this.registerEmail
      }
      fetch(this.state.baseURL + '/register.php', this.state.fetchObj(sendData))
      .then(res=>res.json()).then(data=>{
        if(data[0]){
          this.state.loggedinUser = data[1]
          this.state.token = data[1].passhash
          this.state.loggedinUserHash = data[1].passhash
          this.state.loggedinUserID = data[1].id
          this.state.loggedinUserAvatar = data[1].avatar
          this.state.loggedinUserName = data[1].name
          this.state.loggedinUserEmail = data[1].email
          this.state.admin = !!(+data[1].admin)
          this.state.setCookie()
          this.state.loadLoggedInUserData()
          this.state.closeModals()
          this.state.loggedin = true
        }else{
          let el = document.querySelector('#registerError')
          el.style.display = 'block'
          el.innerHTML = data[2]
        }
      })
    }
  },
  mounted(){
    this.$refs.registerModal.focus()
  }
}

</script>

<style scoped>
  .unavailable{
    background: #400;
    width: 400px;
    display: block;
    margin-left: auto;
    margin-right: auto;
    color: #f44;
  }
  .good{
    background: #042!important;
    color: #4fc!important;
  }
  .noGood{
    background: #400;
    color: #f44;
  }
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
  .registerInput{
    margin: 3px;
  }
  #registerCancel{
    position: absolute;
    right: 0;
    margin-right: 50px;
    margin-top: 50px;
  }
  .register{
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
