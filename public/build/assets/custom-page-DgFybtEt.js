import{j as r}from"./ui-CwCFFJcI.js";import{K as N,L as v}from"./app-BKwi3L82.js";import w from"./Header-BPon5MQw.js";import C from"./Footer-iSMqEHUk.js";import{u as k}from"./use-favicon-CXvuYKDg.js";import"./vendor-B1hewrmX.js";import"./utils-DVuJ_tgg.js";import"./menu-Bn38g0vn.js";import"./mail-kqLDz7Sg.js";import"./phone-BypfzvDj.js";import"./map-pin-BOR2HQRU.js";import"./instagram-C9Gzaj7a.js";import"./twitter-BHg7rIwL.js";function T(){var i,c,n,l,p,d,x,h,f,g,u,b,j;const y=`
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
      color: #1f2937;
      font-weight: 600;
      margin-top: 2rem;
      margin-bottom: 1rem;
    }
    
    .prose h1 { font-size: 2.25rem; }
    .prose h2 { font-size: 1.875rem; }
    .prose h3 { font-size: 1.5rem; }
    
    .prose p {
      margin-bottom: 1.5rem;
      line-height: 1.75;
    }
    
    .prose ul, .prose ol {
      margin: 1.5rem 0;
      padding-left: 1.5rem;
    }
    
    .prose li {
      margin-bottom: 0.5rem;
    }
    
    .prose a {
      color: var(--primary-color);
      text-decoration: underline;
    }
    
    .prose blockquote {
      border-left: 4px solid var(--primary-color);
      padding-left: 1rem;
      margin: 1.5rem 0;
      font-style: italic;
      background-color: #f9fafb;
      padding: 1rem;
    }
    
    .prose img {
      max-width: 100%;
      height: auto;
      border-radius: 0.5rem;
      margin: 1.5rem 0;
    }
  `,{page:t,customPages:_=[],settings:e}=N().props,a=((c=(i=e==null?void 0:e.config_sections)==null?void 0:i.theme)==null?void 0:c.primary_color)||"#3b82f6",m=((l=(n=e==null?void 0:e.config_sections)==null?void 0:n.theme)==null?void 0:l.secondary_color)||"#8b5cf6",s=((d=(p=e==null?void 0:e.config_sections)==null?void 0:p.theme)==null?void 0:d.accent_color)||"#10b981";return k(),r.jsxs(r.Fragment,{children:[r.jsxs(v,{children:[r.jsx("title",{children:t.meta_title||t.title}),t.meta_description&&r.jsx("meta",{name:"description",content:t.meta_description}),r.jsx("style",{children:y})]}),r.jsxs("div",{className:"min-h-screen bg-white",style:{"--primary-color":a,"--secondary-color":m,"--accent-color":s,"--primary-color-rgb":((x=a.replace("#","").match(/.{2}/g))==null?void 0:x.map(o=>parseInt(o,16)).join(", "))||"59, 130, 246","--secondary-color-rgb":((h=m.replace("#","").match(/.{2}/g))==null?void 0:h.map(o=>parseInt(o,16)).join(", "))||"139, 92, 246","--accent-color-rgb":((f=s.replace("#","").match(/.{2}/g))==null?void 0:f.map(o=>parseInt(o,16)).join(", "))||"16, 185, 129"},children:[r.jsx(w,{"max-w-7xl":!0,"mx-auto":!0,settings:e,customPages:_,sectionData:((u=(g=e==null?void 0:e.config_sections)==null?void 0:g.sections)==null?void 0:u.find(o=>o.key==="header"))||{},brandColor:a}),r.jsx("main",{className:"pt-16",children:r.jsx("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12",children:r.jsxs("div",{className:"max-w-4xl mx-auto",children:[r.jsxs("header",{className:"text-center mb-12",children:[r.jsx("h1",{className:"text-4xl font-bold text-gray-900 mb-4",children:t.title}),r.jsx("div",{className:"w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"})]}),r.jsx("article",{className:"max-w-none",children:r.jsx("div",{className:"text-gray-700 leading-relaxed text-lg",dangerouslySetInnerHTML:{__html:t.content}})})]})})}),r.jsx(C,{settings:e,sectionData:((j=(b=e==null?void 0:e.config_sections)==null?void 0:b.sections)==null?void 0:j.find(o=>o.key==="footer"))||{},brandColor:a})]})]})}export{T as default};
