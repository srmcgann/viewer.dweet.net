<template>
  <Viewer v-if="state.viewerModalVisible" :state="state" :src="this.state.viewerSrc"/>
  <Settings v-if="state.settingsModalVisible" :state="state"/>
  <Register v-if="state.registerModalVisible" :state="state"/>
  <Login v-if="state.loginModalVisible" :state="state"/>
  <Header :state="state"/>
  <div v-if="state.loggedin" v-for="i in 1" class="App">
    <Dropzone :state="state" :caption="'zone ' + i"/>
  </div>
  <Main v-else :state="state" />
</template>

<script>
import Header from './components/Header'
import Main from './components/Main'
import Settings from './components/Settings'
import Login from './components/Login'
import Viewer from './components/Viewer'
import Register from './components/Register'
import Dropzone from './components/Dropzone'
export default {
  components: {
    Header,
    Main,
    Dropzone,
    Login,
    Viewer,
    Settings,
    Register
  },
  name: 'App',
  data(){
    return {
      state: {
        baseURL: 'https://viewer.dweet.net',
        rootDomain: 'viewer.dweet.net',
        loggedin: false,
        assetsURL: 'https://assets.dweet.net/misc',
        fileViewerURL: 'https://viewer.dweet.net/viewer',
        admin: false,
        loggedinUserAvatar: '',
        loggedinUserEmail: '',
        loggedinUserName: '',
        loggedinUserHash: '',
        parentFolderDropTarget: null,
        cursorX: null,
        cursorY: null,
        loadLoggedInUserData: null,
        loggedinUserBasicIcons: '',
        loggedinUserSnapToGrid: '',
        positionFilesAbsolutely: null,
        loggedinUserID: '',
        curFileDragging: null,
        loggedinUserFiles: [],
        loggedinUserLocation: '',
        viewerSrc: '',
        decToAlpha: null,
        alphaToDec: null,
        view: null,
        setting: null,
        dropTarget: '/',
        fetchObj: null,
        getSuffix: null,
        showModal: null,
        closeModals: null,
        login: null,
        token: '',
        register: null,
        logout: null,
        loginModalVisible: false,
        registerModalVisible: false,
        settingsModalVisible: false,
        viewerModalVisible: false,
        setCookie: null
      }
    }
  },
  methods:{
    login(){
      this.closeModals()
      this.showModal('login')
    },
    logout(){
      this.clearCookie()
      this.state.loggedinUserName = ''
      this.state.loggedinUserID = ''
      this.state.loggedinUserHash = ''
      this.state.loggedinUserLocation = ''
      this.state.loggedinUserFiles = []
      this.state.loggedin = false
      window.location.reload()
    },
    alphaToDec(val){
      let pow=0
      let res=0
      let cur, mul
      while(val!=''){
        cur=val[val.length-1]
        val=val.substring(0,val.length-1)
        mul=cur.charCodeAt(0)<58?cur:cur.charCodeAt(0)-(cur.charCodeAt(0)>96?87:29)
        res+=mul*(62**pow)
        pow++
      }
      return res
    },
    decToAlpha(n){
      let alphabet='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
      let ret='', r
      while(n){
        ret = alphabet[Math.round((n/62-(r=n/62|0))*62)|0] + ret
        n=r
      }
      return ret == '' ? '0' : ret
    },
    getAvatar(id){
      if(typeof this.state.userInfo[id] == 'undefined' || !this.state.userInfo[id].avatar){
        return this.state.defaultAvatar
      } else {
        this.state.userInfo[id].avatar = this.state.userInfo[id].avatar.replace(' ','')
        return this.state.userInfo[id].avatar
      }
    },
    setCookie(){
      let cookies = document.cookie
      cookies.split(';').map(v=>{
        document.cookie = v + '; expires=' + (new Date(0)).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      })
      document.cookie = 'loggedinUser=' + this.state.loggedinUserName + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'loggedinUserLocation=' + this.state.loggedinUserLocation + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      //document.cookie = 'basicIcons=' + this.state.loggedinUserBasicIcons + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      //document.cookie = 'snapToGrid=' + this.state.loggedinUserSnapToGrid + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'minimized=' + this.state.minimized + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'loggedinUserID=' + this.state.loggedinUserID + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'loggedinUserHash=' + this.state.loggedinUserHash + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      document.cookie = 'token=' + this.state.token + '; expires=' + (new Date((Date.now()+3153600000000))).toUTCString() + '; path=/; domain=' + this.state.rootDomain
    },
    fetchObj(sendData){
      let fetchObj
      if(location.href.indexOf('8000')===-1){
        fetchObj = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }
      }else{
        fetchObj = {
          method: 'POST',
          body: JSON.stringify(sendData),
        }
      }
      return fetchObj
    },
    loadLoggedInUserData(){
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash
      }
      fetch(this.state.baseURL + '/loadUserData.php', this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        if(data[0]){
          this.state.loggedinUserFiles = [] //data[1]
          this.$nextTick(()=>{
            data[1].map(v=>{
              v.private = !!(+v.private)
            })
            this.state.loggedinUserFiles = data[1]
            let s = window.location.origin+'/'+this.decToAlpha(this.state.loggedinUserID)+this.state.loggedinUserLocation
            window.history.pushState(s, null, s)
            this.$nextTick(()=>this.state.positionFilesAbsolutely())
          })
        }else{
          console.log('loadUserData[App.vue]',data)
          alert('there was an error loading user data. consarnit!')
        }
      })
    },
    register(){
      this.closeModals()
      this.showModal('register')
    },
    showModal(modal){
      switch(modal){
        case 'login':
          this.state.loginModalVisible = true
        break
        case 'register':
          this.state.registerModalVisible = true
        break
        case 'settings':
          this.state.settingsModalVisible = true
        break
        case 'viewer':
          this.state.viewerModalVisible = true
        break
      }
    },
    view(src){
      this.closeModals
      this.state.viewerSrc = src
      this.showModal('viewer')
    },
    settings(){
      this.closeModals()
      this.showModal('settings')
    },
    closeModals(){
      this.state.loginModalVisible = false
      this.state.registerModalVisible = false
      this.state.settingsModalVisible = false
      this.state.viewerModalVisible = false
    },
    clearCookie(){
      let cookies = document.cookie
      cookies.split(';').map(v=>{
        document.cookie = v + '; expires=' + (new Date(0)).toUTCString() + '; path=/; domain=' + this.state.rootDomain
      })
    },
    positionFilesAbsolutely(reset){
      this.$nextTick(()=>{
        if(reset){
          this.state.loggedinUserFiles.map(v => {
            v.fileDiv.style.position = 'initial'
            v.fileDiv.style.left = 'initial'
            v.fileDiv.style.top = 'initial'
          })
        }
        this.$nextTick(() => {
          this.state.loggedinUserFiles.map(v => {
            v.rect = v.fileDiv.getBoundingClientRect()
            v.dragHandleRect = v.dragHandle.getBoundingClientRect()
          })
          this.state.loggedinUserFiles.map(v => {
            let rect = v.rect//fileDiv.getBoundingClientRect()
            v.fileDiv.style.position = 'absolute'
            v.fileDiv.style.left = (rect.x - 11) + 'px'
            v.fileDiv.style.top = (rect.y - 82) + 'px'
          })
        })
      })
    },
    checkCookie(){
      let parts = document.cookie.split(';')
      let user =''
      let passhash = ''
      let id = ''
      let location = decodeURIComponent(window.location.pathname)
      parts.map(v=>{
        v = v.trim()
        let pair = v.split('=')
        switch(pair[0]){
          case 'loggedinUser':
            user = pair[1]
          break
          case 'loggedinUserID':
            id = pair[1]
          break
          case 'loggedinUserHash':
            passhash = pair[1]
          break
        }
      })
      if(user && passhash && id){
        let sendData = {
          user,
          passhash,
          location
        }
        fetch(this.state.baseURL + '/cookieLogin.php',  this.state.fetchObj(sendData))
        .then(json=>json.json()).then(data=>{
          if(data[0]){
            console.log('cookieLogin.php[App.vue]',data)
            this.state.loggedin = true
            this.state.loggedinUser = data[1]
            this.state.token = data[1].passhash
            this.state.loggedinUserHash = data[1].passhash
            this.state.loggedinUserID = data[1].id
            this.state.loggedinUserAvatar = data[1].avatar
            this.state.loggedinUserBasicIcons = eval(data[1].basicIcons)//!!(+data[1].basicIcons)
            this.state.loggedinUserSnapToGrid = eval(data[1].snapToGrid)//!!(+data[1].snapToGrid)
            this.state.loggedinUserName = data[1].name
            this.state.loggedinUserLocation = data[1].currentLocation
            this.state.loggedinUserEmail = data[1].email
            this.state.admin = !!(+data[1].admin)
            this.state.setCookie()
            //window.location.reload
            this.state.loadLoggedInUserData()
            this.state.closeModals()
          }else{
            this.clearCookie()
          }
        })
      }
    },
    moveToParent(src){
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash,
        src: src.id
      }
      console.log(sendData)
      fetch(this.state.baseURL + '/moveFileToParent.php',  this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        console.log(data)
        if(data[0]){
          this.state.loggedinUserFiles = this.state.loggedinUserFiles.filter(v=>{
            return v.id != src.id
          })
          this.positionFilesAbsolutely(true)
        }
      })
    },
    moveFile(src, dest){
      let sendData = {
        user: this.state.loggedinUserName,
        passhash: this.state.loggedinUserHash,
        src: src.id,
        dest: dest.id
      }
      console.log(sendData)
      fetch(this.state.baseURL + '/moveFile.php',  this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        console.log(data)
        if(data[0]){
          this.state.loggedinUserFiles = this.state.loggedinUserFiles.filter(v=>{
            return v.id != src.id
          })
          this.positionFilesAbsolutely(true)
        }
      })
    },
    loadAsset(slug){
      let sendData = {
        slug
      }
      fetch(this.state.baseURL + '/loadAsset.php',  this.state.fetchObj(sendData))
      .then(json=>json.json()).then(data=>{
        if(data[0]){
          console.log('loadAsset[viewer].php[App.vue]',data)
          this.state.loggedinUserFiles = [] //data[1]
          this.$nextTick(()=>{
            this.state.loggedin = true
            this.state.loggedinUserFiles = data[1]
            this.$nextTick(()=>this.state.positionFilesAbsolutely())
          })
        }else{
          console.log('loadAsset[viewer][App.vue]',data)
          alert('there was an error loading the asset. consarnit!')
        }
      })
    },
    setupListeners(){
      document.body.onmousemove = e => {
        e.preventDefault()
        e.stopPropagation()
        if(this.state.curFileDragging != null && this.state.button){
          if(this.state.loggedinUserLocation != '/'){
            this.state.parentFolderDropTarget.style.backgroundColor = '#f004'
          }
          this.state.loggedinUserFiles.map(v=>{
            v.dragHandle.style.backgroundColor = '#f004'
            v.rect = v.fileDiv.getBoundingClientRect()
            v.dragHandleRect = v.dragHandle.getBoundingClientRect()
          })
          this.state.curFileDragging.style.left = (this.state.cursorX - 5 + e.pageX) + 'px'
          this.state.curFileDragging.style.top = (this.state.curFileDragging.dropzone.scrollTop + this.state.cursorY - 80 + e.pageY) + 'px'
          this.state.loggedinUserFiles.map(v=>{
            if(e.pageX > v.dragHandleRect.x && e.pageX < v.dragHandleRect.x + v.dragHandleRect.width &&
              e.pageY > v.dragHandleRect.y - this.state.curFileDragging.dropzone.scrollTop && e.pageY < v.dragHandleRect.y + v.dragHandleRect.height - this.state.curFileDragging.dropzone.scrollTop){
              if(v.type == 'folder'){
                v.dragHandle.style.backgroundColor = '#0f44'
              }
            }
          })
          if(this.state.loggedinUserLocation != '/'){
            let pfdhRect = this.state.parentFolderDropTarget.getBoundingClientRect()
            if(e.pageX > pfdhRect.x && e.pageX < pfdhRect.x + pfdhRect.width &&
              e.pageY > pfdhRect.y - this.state.curFileDragging.dropzone.scrollTop && e.pageY < pfdhRect.y + pfdhRect.height - this.state.curFileDragging.dropzone.scrollTop){
              this.state.parentFolderDropTarget.style.backgroundColor = '#0f44'
            }
          }
        }
      }
      window.onresize = () => {
        this.positionFilesAbsolutely(true) 
      }
      document.body.onmouseup=e=>{
        if(this.state.curFileDragging != null && e.button == 0){
          this.state.loggedinUserFiles.map(v=>{
            if(v.id != this.state.curFileDragging.file.id && e.pageX > v.dragHandleRect.x && e.pageX < v.dragHandleRect.x + v.dragHandleRect.width &&
              e.pageY > v.dragHandleRect.y && e.pageY < v.dragHandleRect.y + v.dragHandleRect.height){
              if(v.type == 'folder'){
                console.log('moveFile -> ', this.state.curFileDragging.file, v)
                let cfdf = this.state.curFileDragging.file
                this.moveFile(cfdf, v)
              }
            }
          }) 
        }
        if(this.state.loggedinUserLocation != '/'){
          let pfdhRect = this.state.parentFolderDropTarget.getBoundingClientRect()
          if(e.pageX > pfdhRect.x && e.pageX < pfdhRect.x + pfdhRect.width &&
            e.pageY > pfdhRect.y && e.pageY < pfdhRect.y + pfdhRect.height){
            let cfdf = this.state.curFileDragging.file
            this.moveToParent(cfdf)
          }
        }

        if(e.button == 0){
          this.state.button = false
          this.state.curFileDragging.file = null
          this.state.curFileDragging = null
        }
      }
    }
  },
  mounted(){
    
    this.state.view = this.view
    this.state.login = this.login
    this.state.logout = this.logout
    this.state.register = this.register
    this.state.settings = this.settings
    this.state.fetchObj = this.fetchObj
    this.state.getSuffix = this.getSuffix
    this.state.setCookie = this.setCookie
    this.state.decToAlpha = this.decToAlpha
    this.state.alphaToDec = this.alphaToDec
    this.state.showModals = this.showModals
    this.state.closeModals = this.closeModals
    this.state.loadLoggedInUserData = this.loadLoggedInUserData
    this.state.positionFilesAbsolutely = this.positionFilesAbsolutely
    //this.checkCookie()
    //this.setupListeners()
    this.loadAsset(window.globalAsset)
  }
}

</script>

<style>
html, body{
  background: #000;
  font-family: courier;
  font-size: 16px;
  min-height: 100vh;
  width: 100vw;
  overflow: hidden;
  margin: 0;
  line-height: 1.1em;
}
.folderIcon{
  width: 125px;
  height: 85px;
  border: none;
  background-color: #2000!important;
  background-position: center center!important;
  background-size: contain!important;
  background-repeat: no-repeat!important;
  background-image: url(https://jsbot.cantelope.org/uploads/2jP7OJ.png)!important;
}
.fileDiv{
  padding: 0px;
  padding-top:20px;
  background: #000;
  position: relative;
  display: inline-block;
  max-width: 125px;
  min-height: 80px;
  margin: 10px;
  align-self: flex-start;
  border-radius: 5px;
}
.basicTextInput:focus{
  outline: none;
}
.basicTextInput{
  background: #000a;
  border: none;
  color: #4f8;
  border-bottom: 1px solid #0fa;
  min-width: 400px;
}
.inputForm{
  width: 500px;
  position: absolute;
  left: calc(50% - 25px);
  top: calc(50% - 25px);
  transform: translate(-50%, -50%);
  display: inline-block;
  background: #3338;
  padding-bottom: 20px;
}
.float{
  z-index: 50;
  position: relative;
}
/* Customize the label (the checkboxLabel) */
.checkboxLabel {
  display: inline-block;
  position: relative;
  padding-left: 35px;
  margin-bottom: -2px;
  margin-left: 30px;
  margin-top: 3px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.checkboxLabel input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  border: 1px solid #2468;
  background-color: #123;
}

/* On mouse-over, add a grey background color */
.checkboxLabel:hover input ~ .checkmark {
  background-color: #234;
}

/* When the checkbox is checked, add a blue background */
.checkboxLabel input:checked ~ .checkmark {
  background-color: #086;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.checkboxLabel input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.checkboxLabel .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
.button{
  background: #4fc;
  border-radius: 30px;
  border: none;
  margin: 5px;
  cursor: pointer;
  min-width: 80px;
  display: inline-block;
}
.button:focus{
  outline: none;
}
.cancelButton{
  background: #400;
  color: #f00;
}
.inputForm{

}
.App{
  width: 100vw;
}
</style>
