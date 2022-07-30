Gem::Specification.new do |s|
  s.name               = "a2"
  s.version            = "0.0.1"
  s.default_executable = "a2"

  s.required_rubygems_version = Gem::Requirement.new(">= 0") if s.respond_to? :required_rubygems_version=
  s.authors = ["Jubayed IT"]
  s.date = %q{2010-04-03}
  s.description = %q{A2 Platform }
  s.email = %q{nick@quaran.to}
  s.files = ["Rakefile", "ruby/src/a2.rb", "ruby/src/a2/translator.rb", "ruby/bin/a2"]
  s.homepage = %q{http://rubygems.org/gems/a2}
  s.require_paths = ["ruby/src"]
  s.rubygems_version = %q{1.6.2}
  s.summary = %q{a2!}

  s.add_runtime_dependency("webrick",               "~> 1.7")

  if s.respond_to? :specification_version then
    s.specification_version = 3

    if Gem::Version.new(Gem::VERSION) >= Gem::Version.new('1.2.0') then
    else
    end
  else

  end
end

