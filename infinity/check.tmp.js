// TEMP CHECK - is aboutMerchant.js patched on the live proxy now?
const http = require('http');
const vm = require('vm');
const fs = require('fs');
const zlib = require('zlib');
const HOST = 'lottogamez.gamer.free';
const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';

const ctx = {}; vm.createContext(ctx);
vm.runInContext(fs.readFileSync('C:/xampp/htdocs/infinity/aes.js', 'utf8'), ctx);
const tn = d => { const e = []; d.replace(/(..)/g, x => e.push(parseInt(x, 16))); return e; };
const th = d => d.map(b => (b < 16 ? '0' : '') + b.toString(16)).join('');

function req(path) {
  return new Promise((resolve, reject) => {
    const r = http.request({ host: HOST, port: 80, path, method: 'GET', headers: { 'User-Agent': UA, 'Accept': '*/*', 'Accept-Encoding': 'gzip' } }, res => {
      const chunks = []; res.on('data', c => chunks.push(c));
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: Buffer.concat(chunks) }));
    });
    r.on('error', reject); r.setTimeout(25000, () => r.destroy(new Error('timeout'))); r.end();
  });
}

(async () => {
  const c1 = await req('/m/index.html');
  let ch = c1.body; if ((c1.headers['content-encoding'] || '').includes('gzip')) ch = zlib.gunzipSync(ch);
  const hexes = [...ch.toString().matchAll(/toNumbers\("([0-9a-f]+)"\)/g)].map(m => m[1]);
  if (hexes.length < 3) { console.log('challenge issue:', ch.toString().slice(0, 120)); return; }
  const cookie = '__test=' + th(ctx.slowAES.decrypt(tn(hexes[2]), 2, tn(hexes[0]), tn(hexes[1])));
  const H = { 'User-Agent': UA, 'Accept': '*/*', 'Accept-Encoding': 'gzip, deflate', Cookie: cookie };

  const r = await req('/res/aboutMerchant.js');
  let b = r.body; const enc = r.headers['content-encoding'] || '';
  if (enc.includes('gzip')) b = zlib.gunzipSync(b);
  const s = b.toString();
  console.log('status:', r.status, '| enc:', enc || 'identity', '| bytes:', b.length);
  if (r.status === 302 || r.status === 403) { console.log('still WAF-flagged for my IP:', s.slice(0, 120)); return; }
  console.log('patched (affiliateRedirect:!1):', s.includes('affiliateRedirect:!1'));
  console.log('UNPATCHED (affiliateRedirect:!0):', s.includes('affiliateRedirect:!0'));
  console.log('X-Proxy-Cache:', r.headers['x-proxy-cache'] || '-');
})();
