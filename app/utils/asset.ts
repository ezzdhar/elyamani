/**
 * Resolves static asset paths relative to the application's runtime baseURL.
 * Ensures assets work both in development ('/') and deployed subpaths (e.g. GitHub Pages '/elyamani/').
 */
export function withBase(path: string): string {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('data:')) {
    return path
  }
  const config = useRuntimeConfig()
  const base = config.app?.baseURL || '/'
  const cleanBase = base.endsWith('/') ? base : `${base}/`
  const cleanPath = path.replace(/^\//, '')
  return `${cleanBase}${cleanPath}`
}
