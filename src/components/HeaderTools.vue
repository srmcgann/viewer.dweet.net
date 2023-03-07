<template>
  <div class="headerTools">
    <div v-if="state.loggedin">
      <label :for="'basicIconsCheckbox'" class="checkboxLabel headerToolsCheckboxes" style="transform: scale(.75);" :title="'use/don\'t use basic icons\n(by default, gif thumbnails are animated for example\n but with many of them this can cause performance problems'">
        <input type="checkbox" id="basicIconsCheckbox" v-model="state.loggedinUserBasicIcons" @input="toggleUserProperty('basicIcons')">
        <span class="checkmark" style=";border: 1px solid #fff8"></span>
        <span class="headerToolsCheckboxLabel" style="margin-top: 30px">basic icons</span>
      </label>
      <label :for="'snapToGridCheckbox'" class="checkboxLabel headerToolsCheckboxes" style="transform: scale(.75); margin-top:35px;" :title="'toggle auto-align icons to grid'">
        <input type="checkbox" id="snapToGridCheckbox" v-model="state.loggedinUserSnapToGrid" @input="toggleUserProperty('snapToGrid')">
        <span class="checkmark" style=";border: 1px solid #fff8"></span>
        <span class="headerToolsCheckboxLabel" style="margin-top: 30px">snap to grid</span>
      </label>
      <button
        v-if="0"
        class="button"
        @click="logout()"
      >logout</button><br>
      <button
        v-if="0"
        class="button"
        @click="settings()"
      >settings</button>
    </div>
    <div v-else>
      <button
        class="button"
        v-if="0"
        :class="{'float': state.registerModalVisible}"
        @click="login()"
      >login</button><br>
      <button
        class="button"
        v-if="0"
        :class="{'float': state.loginModalVisible}"
        @click="register()"
      >register</button>
    </div>
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
    toggleUserProperty(property){
      this.$nextTick(()=>this.state.setCookie())
      return
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash,
        property,
        value: this.state['loggedinUser'+(property.split('').map((v,i)=>i?v:v.toUpperCase()).join(''))]==false ? 1 : 0
      }
      console.log(sendData)
      fetch(this.state.baseURL + '/setUserProperty.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        console.log(data)
        //if(data[0]) this.file.private = !(+this.file.private)
      })
    },
    settings(){
      this.state.closeModals()
      this.state.settings()
    }
  },
  mounted(){
    //this.state.loginModalVisible = true
  }
}

</script>

<style scoped>
  .headerToolsCheckboxes{
    position: absolute;
    margin-left: -30px;
  }
  .headerToolsCheckboxLabel{
    font-size: 10px;
    position: absolute;
    margin-left: -72px;
    line-height: 1em;
    min-width: 73px;
  }
  .headerTools{
    position: absolute;
    top:0;
    display: none;
    right: 0;
    padding: 5px;
    color: #fff;
  }
</style>
