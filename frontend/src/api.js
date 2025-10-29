const API_BASE = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8001') + '/api/'

export async function register(payload){
  const res = await fetch(API_BASE + 'accounts/register/', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  if(!res.ok){
    const txt = await res.text()
    throw new Error(txt || res.statusText)
  }
  return res.json()
}
