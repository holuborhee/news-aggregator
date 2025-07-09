import Link from "next/link";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

export default async function Header() {
  const cookieStore = await cookies();
  const token = cookieStore.get("token")?.value;
  // const email = cookieStore.get("email")?.value;
  // const name = cookieStore.get("name")?.value;

  return (
    <header className="p-4 border-b flex justify-between items-center">
      <Link href="/" className="text-xl font-semibold">
        News Aggregator
      </Link>
      {token ? (
        <Link href="/feeds">My feeds</Link>
      ) : (
        <div className="space-x-4">
          <Link href="/login">Login</Link>
          <Link href="/register">Sign Up</Link>
        </div>
      )}
    </header>
  );
}
