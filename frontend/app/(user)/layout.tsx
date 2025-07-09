// app/(user)/layout.tsx
import Link from "next/link";
import { cookies } from "next/headers";
import { ReactNode } from "react";

export default async function UserLayout({
  children,
}: {
  children: ReactNode;
}) {
  const cookieStore = await cookies();
  const name = cookieStore.get("name")?.value;
  const email = cookieStore.get("email")?.value;

  return (
    <div className="min-h-screen flex flex-col">
      <header className="p-4 border-b flex justify-between items-center">
        <div>
          <Link href="/feeds" className="mr-4 font-bold text-blue-600">
            Feeds
          </Link>
          <Link href="/preferences" className="text-blue-600">
            Preferences
          </Link>
        </div>
        <div className="flex items-center gap-4">
          <div className="text-sm text-gray-700">
            <p>{name}</p>
            <p>{email}</p>
          </div>
          <form action="/logout" method="POST">
            <button className="text-sm text-red-600">Logout</button>
          </form>
        </div>
      </header>
      <main className="flex-1 p-6">{children}</main>
    </div>
  );
}
