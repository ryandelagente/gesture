import { useEffect, useState } from 'react';
import { X, ArrowRight, CheckCircle2, Users, FolderPlus, Code2, Bug } from 'lucide-react';

const STORAGE_KEY = 'gesture.tour.dismissed';

const steps = [
  {
    icon: <Users className="h-6 w-6 text-blue-600" />,
    title: 'Invite your team',
    body: 'Add team members so you can assign bugs and tasks. Go to Workspaces → invite by email — they get an account link.',
    cta: 'Open Workspaces',
    href: '/workspaces',
  },
  {
    icon: <FolderPlus className="h-6 w-6 text-blue-600" />,
    title: 'Create your first project',
    body: 'A project is one client site. Pick a Service Type (SEO / Web Development / Google Ads) — we’ll auto-add an onboarding checklist.',
    cta: 'Create project',
    href: '/projects',
  },
  {
    icon: <Code2 className="h-6 w-6 text-blue-600" />,
    title: 'Install the feedback widget',
    body: 'Drop a one-line script on any client site. Clients can pin issues, leave screenshots & videos with no login — they appear in your Bugs list.',
    cta: 'See widget setup',
    href: '/tutorials#bug-widget',
  },
  {
    icon: <Bug className="h-6 w-6 text-blue-600" />,
    title: 'You’re ready',
    body: 'Bugs come in → AI triages them → SLA tracks them → Slack/Teams notifies you. Open the Bugs board anytime.',
    cta: 'Open Bugs',
    href: '/bugs',
  },
];

export function OnboardingTour() {
  const [step, setStep] = useState(0);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    try {
      if (!localStorage.getItem(STORAGE_KEY)) setOpen(true);
    } catch (e) {
      // localStorage unavailable
    }
  }, []);

  function dismiss() {
    try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
    setOpen(false);
  }

  function next() {
    if (step < steps.length - 1) setStep(step + 1);
    else dismiss();
  }

  if (!open) return null;

  const s = steps[step];
  const isLast = step === steps.length - 1;

  return (
    <div
      className="fixed inset-0 z-[1000] flex items-center justify-center p-4"
      style={{ background: 'rgba(0,0,0,.45)' }}
      onClick={(e) => { if (e.target === e.currentTarget) dismiss(); }}
    >
      <div className="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
        <div className="px-6 pt-5 pb-3 flex items-start justify-between border-b border-gray-100">
          <div className="flex items-center gap-3">
            <div className="rounded-lg bg-blue-50 p-2">{s.icon}</div>
            <div>
              <div className="text-xs uppercase tracking-wide text-blue-600 font-semibold">
                Step {step + 1} of {steps.length}
              </div>
              <h3 className="text-lg font-semibold text-gray-900">{s.title}</h3>
            </div>
          </div>
          <button onClick={dismiss} className="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close">
            <X className="h-5 w-5" />
          </button>
        </div>

        <div className="px-6 py-4">
          <p className="text-sm text-gray-600 leading-relaxed">{s.body}</p>

          <div className="flex gap-1.5 mt-4">
            {steps.map((_, i) => (
              <div
                key={i}
                className={`h-1.5 rounded-full transition-all ${i === step ? 'w-8 bg-blue-600' : i < step ? 'w-4 bg-blue-300' : 'w-4 bg-gray-200'}`}
              />
            ))}
          </div>
        </div>

        <div className="px-6 py-4 bg-gray-50 flex justify-between items-center border-t border-gray-100">
          <button onClick={dismiss} className="text-sm text-gray-500 hover:text-gray-700">
            Skip tour
          </button>
          <div className="flex gap-2">
            <a
              href={s.href}
              className="text-sm border border-blue-200 text-blue-700 hover:bg-blue-50 px-3 py-1.5 rounded-md transition-colors"
              onClick={dismiss}
            >
              {s.cta}
            </a>
            <button
              onClick={next}
              className="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md flex items-center gap-1.5 transition-colors"
            >
              {isLast ? (<>Done <CheckCircle2 className="h-4 w-4" /></>) : (<>Next <ArrowRight className="h-4 w-4" /></>)}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
