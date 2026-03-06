const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.vue')) results.push(file);
        }
    });
    return results;
}

const vueFiles = walk('resources/js');

let totalReplaced = 0;

for (const file of vueFiles) {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // Regex to match: :class="isDark ? 'darkClasses' : 'lightClasses'"
    // We'll replace it with static classes. Since it's inside an attribute, we might need to merge it with an existing class attribute, 
    // or just change it to class="lightClasses dark:dark1 dark:dark2" if there's no other class attribute.
    // However, the easiest way without parsing HTML is replacing just the Vue binding segment.
    
    // Pattern: :class="isDark ? 'A' : 'B'"
    // Replace with standard class attributes inline (but wait, there might be another `class="..."` on the same element)
    // Actually, Vue runtime merges `:class=" 'static string' "` with `class="..."` perfectly fine!
    // So we can just replace `:class="isDark ? 'A' : 'B'"` with `:class="'B dark:A1 dark:A2'"` !!
    
    content = content.replace(/:class\s*=\s*(["'])isDark\s*\?\s*(['"])(.*?)\2\s*:\s*(['"])(.*?)\4\1/g, (match, q1, q2, darkClasses, q4, lightClasses) => {
        let darks = darkClasses.split(' ').filter(Boolean).map(c => c.startsWith('dark:') ? c : `dark:${c}`).join(' ');
        let merged = `${lightClasses} ${darks}`.trim();
        totalReplaced++;
        return `class="${merged}"`;
    });

    // Also handle :class="[..., isDark ? 'A' : 'B']" which is harder, but maybe rare.

    if (content !== original) {
        // Now, we replaced `:class` with `class=`. If the element already has a `class` attribute, we'll have two `class="..."` attributes, which is invalid HTML/Vue.
        // We can fix multiple class attributes by merging them.
        content = content.replace(/(class="[^"]*")\s+(class="[^"]*")/g, (m, c1, c2) => {
            let v1 = c1.slice(7, -1);
            let v2 = c2.slice(7, -1);
            return `class="${v1} ${v2}"`;
        });
        
        fs.writeFileSync(file, content, 'utf8');
        console.log('Updated:', file);
    }
}

console.log('Total replacements:', totalReplaced);
