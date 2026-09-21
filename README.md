# Content Preflight Checker

The app from the Laravel News course on TypeSafe AI's Jev model and the Laravel AI SDK.

You give it a brief and a short tutorial draft. It asks Jev three questions: does the draft deliver what the brief promised, what format is it, and how clear are the steps. PHP turns those answers into one of three recommendations: `ready-for-editor`, `revise` or `needs-review`. A person makes the final call. Nothing is published.

It does not write articles, check facts or run the code in a draft.

## One branch per episode

| Branch | What it holds |
|---|---|
| `starter` | A fresh Laravel 13 app with a basic sign in, the editor permission and the empty review screen. No starter kit and no AI SDK yet. |
| `episode-1` | The Laravel AI SDK, the Jev provider config, sample texts and the first Boolean question |
| `episode-2` | The `CheckContent` service with Boolean, Choice and Score questions |
| `episode-3` | The `PreflightPolicy` rules |
| `episode-4` | The review screen: saved runs, stale checks and the editor's decision |
| `episode-5` | Input limits, a timeout and a safe failure path |
| `episode-6` | Policy tests with fakes and the labelled evaluation sheet |

Each branch is the finished code for that episode. To follow along with an episode, start from the branch before it.

## What you need

- PHP 8.4 or newer
- Composer
- Node.js and npm
- A TypeSafe AI account and API key, for the real model calls. The tests use fakes and need no key.

## Set up

```bash
git clone <repository-url> content-preflight-checker
cd content-preflight-checker
git checkout starter
composer setup
php artisan db:seed
```

`composer setup` installs the PHP and JavaScript packages, creates `.env`, makes the SQLite database, runs the migrations and builds the assets.

From `episode-1` on, open `.env` and add your key:

```dotenv
TYPESAFE_API_KEY=your-server-side-key
```

Keep that key on the server. Do not commit it and do not put it in JavaScript.

Start the app with `composer dev`, or point Laravel Herd or Valet at the folder.

## Sign in

| Email | Password | Role |
|---|---|---|
| `editor@example.com` | `password` | Editor. Can use the review screen. |
| `writer@example.com` | `password` | Not an editor. Gets a 403. |

These accounts are for local use only.

## Run the tests

```bash
php artisan test
```

The tests never call the live model.

## About the SDK version

The classification feature is not in a tagged Laravel AI SDK release yet. This project locks `laravel/ai` to one commit on the `1.x` branch. Treat it as a preview. When a tagged release includes classification, update the package and check every snippet again.

## Real model calls cost money

Each check sends your brief and draft to TypeSafe's hosted service. Use made-up text like the samples in `resources/preflight/samples.php`, not private drafts.
