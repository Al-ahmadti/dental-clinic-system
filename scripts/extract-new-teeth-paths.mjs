import fs from 'fs';

const s = fs.readFileSync('storage/framework/react-odontogram-data.ts', 'utf8');
const marker = 'export const NewTeethPaths = ';
const mStart = s.indexOf(marker);
if (mStart === -1) process.exit(1);
const bracket = s.indexOf('[', mStart);
let i = bracket;
let depth = 0;
let inStr = false;
let strQuote = '';
let escape = false;
for (; i < s.length; i++) {
  const c = s[i];
  if (inStr) {
    if (escape) {
      escape = false;
      continue;
    }
    if (c === '\\') {
      escape = true;
      continue;
    }
    if (c === strQuote) inStr = false;
    continue;
  }
  if (c === '"' || c === "'") {
    inStr = true;
    strQuote = c;
    continue;
  }
  if (c === '[') depth++;
  if (c === ']') {
    depth--;
    if (depth === 0) {
      i++;
      break;
    }
  }
}
const arrText = s.slice(bracket, i).trim();
const arr = new Function(`return ${arrText}`)();
const out = arr.map((t) => ({
  name: t.name,
  type: t.type,
  outline: t.outlinePath,
  shadow: t.shadowPath,
  highlights: Array.isArray(t.lineHighlightPath) ? t.lineHighlightPath : [t.lineHighlightPath],
}));
fs.mkdirSync('app/Support/Odontogram/data', { recursive: true });
fs.writeFileSync('app/Support/Odontogram/data/new_teeth_paths.json', JSON.stringify(out));
console.log('wrote', out.length, 'glyphs');
