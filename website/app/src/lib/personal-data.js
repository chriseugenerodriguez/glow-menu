import * as site from '../data/siteData.js';
import * as pages from '../data/contentPages.js';
import * as options from '../data/menuOptions.js';
const groups = {site, pages, options};
export async function connectPersonal(fetcher = fetch) {
  const signal = AbortSignal.timeout(8000);
  const response = await fetcher('/personal-demo.json', {signal});
  if (!response.ok) throw new Error('Demo configuration unavailable');
  const config = await response.json();
  const data = await fetcher(`${config.url}/rest/v1/demo_content?select=id,payload`, {
    headers: {apikey: config.anonKey, 'Accept-Profile': 'glowmenu'}, signal
  });
  if (!data.ok) throw new Error('Demo content unavailable');
  const rows = await data.json();
  const changes = [];
  for (const [id, group] of Object.entries(groups)) {
    const payload = rows.find(row => row.id === id)?.payload;
    if (!payload) throw new Error('Missing demo content');
    for (const [key, target] of Object.entries(group)) {
      if (typeof target === 'function') continue;
      const value = payload[key];
      if (!value || typeof value !== 'object' || Array.isArray(value) !== Array.isArray(target)) throw new Error('Invalid demo content');
      changes.push({target, value});
    }
  }
  for (const {target, value} of changes) {
    if (Array.isArray(target)) target.splice(0, target.length, ...value);
    else { for (const key of Object.keys(target)) delete target[key]; Object.assign(target, value); }
  }
}
export async function saveInquiry(body) {
  const response = await fetch('/personal-demo.json');
  if (!response.ok) throw new Error('Demo configuration unavailable');
  const config = await response.json();
  const saved = await fetch(`${config.url}/functions/v1/glowmenu-demo-inquiry`, {
    method: 'POST', headers: {'Content-Type': 'application/json', apikey: config.anonKey},
    body: JSON.stringify(body), signal: AbortSignal.timeout(15000)
  });
  if (!saved.ok) throw new Error(saved.status === 429 ? 'Please wait before trying again.' : 'Your demo inquiry could not be saved. Please retry.');
}
