// app/(user)/preferences/page.tsx
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import PreferencesForm from "@/components/PreferencesForm";

async function fetchPreferences(token: string) {
  const res = await fetch(`${process.env.API_URL}/preferences`, {
    headers: { Authorization: `Bearer ${token}` },
    cache: "no-store",
  });
  if (!res.ok) throw new Error("Failed to load preferences");
  return res.json();
}

async function fetchMeta() {
  const res = await fetch(`${process.env.API_URL}/metadata`, {
    cache: "force-cache",
  });
  if (!res.ok) throw new Error("Failed to load meta");

  const meta = await res.json();

  // Group authors by source_slug and include source name
  const groupedAuthors = meta.sources.reduce(
    (acc: Record<string, { source: string; authors: any[] }>, source: any) => {
      acc[source.slug] = {
        source: source.name,
        authors: meta.authors.filter(
          (author: any) => author.source_slug === source.slug
        ),
      };
      return acc;
    },
    {}
  );

  return {
    ...meta,
    groupedAuthors,
  };
}

export default async function PreferencesPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get("token")?.value;
  if (!token) redirect("/login");

  const [prefData, metaData] = await Promise.all([
    fetchPreferences(token),
    fetchMeta(),
  ]);

  return (
    <PreferencesForm
      initialPreferences={prefData.preferences}
      meta={metaData}
    />
  );
}
