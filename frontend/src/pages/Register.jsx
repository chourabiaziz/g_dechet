import React, { useState } from 'react'
import { register } from '../api'

export default function Register(){
  const [email, setEmail] = useState('')
  const [name, setName] = useState('')
  const [prename, setPrename] = useState('')
  const [countryCode, setCountryCode] = useState('+216')
  const [phone, setPhone] = useState('')
  const [password, setPassword] = useState('')
  const [message, setMessage] = useState(null)

  const handleSubmit = async (e) =>{
    e.preventDefault()
    const payload = { email, name, prename, countryCode, phone, plain_password: password }
    try{
      const res = await register(payload)
      setMessage('Registered id=' + res.id)
    }catch(err){
      setMessage('Error: ' + (err.message || 'failed'))
    }
  }

  return (
    <form onSubmit={handleSubmit} style={{maxWidth:420}}>
      <div><label>Email</label><input value={email} onChange={e=>setEmail(e.target.value)} /></div>
      <div><label>First name</label><input value={name} onChange={e=>setName(e.target.value)} /></div>
      <div><label>Last name</label><input value={prename} onChange={e=>setPrename(e.target.value)} /></div>
      <div><label>Country code</label><input value={countryCode} onChange={e=>setCountryCode(e.target.value)} /></div>
      <div><label>Phone</label><input value={phone} onChange={e=>setPhone(e.target.value)} /></div>
      <div><label>Password</label><input type="password" value={password} onChange={e=>setPassword(e.target.value)} /></div>
      <button type="submit">Register</button>
      {message && <div style={{marginTop:10}}>{message}</div>}
    </form>
  )
}
